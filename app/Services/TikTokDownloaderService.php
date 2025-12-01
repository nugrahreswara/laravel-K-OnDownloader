<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokDownloaderService extends BaseDownloaderService
{
    protected array $defaultSettings = [
        'quality' => 'best',
        'format' => 'mp4',
        'audio_only' => false,
        'watermark' => false,
        'embed_metadata' => true
    ];

    protected array $audioFormats = ['mp3', 'm4a', 'wav', 'opus', 'flac'];

    // Available impersonate targets - reduced to most effective ones
    protected array $impersonateTargets = [
        'chrome124',
        'chrome120',
        'edge',
    ];

    public function getVideoInfo(string $url): array
    {
        Log::info('Getting video info for TikTok URL', ['url' => $url]);

        // Try multiple methods with timeout protection
        $commands = [];

        // Try with different impersonate targets (limited to 3)
        foreach ($this->impersonateTargets as $target) {
            $commands[] = [
                'yt-dlp',
                '--dump-json',
                '--no-download',
                '--socket-timeout', '30',
                '--impersonate', $target,
                $url
            ];
        }

        // Fallback without impersonate
        $commands[] = [
            'yt-dlp',
            '--dump-json',
            '--no-download',
            '--socket-timeout', '30',
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            $url
        ];

        $lastError = null;
        foreach ($commands as $index => $command) {
            Log::info("Trying TikTok info method " . ($index + 1) . "/" . count($commands));
            
            // Use executeInfoCommand with short timeout (60 seconds)
            $result = $this->executeInfoCommand($command);
            
            if ($result['success']) {
                $info = json_decode($result['output'], true);
                
                if ($info && isset($info['title'])) {
                    $thumbnail = null;
                    
                    if (isset($info['thumbnail'])) {
                        $thumbnail = $info['thumbnail'];
                    } elseif (isset($info['thumbnails']) && !empty($info['thumbnails'])) {
                        $thumbnails = $info['thumbnails'];
                        $thumbnail = end($thumbnails)['url'] ?? null;
                    }

                    Log::info('Successfully got TikTok video info', [
                        'title' => $info['title'],
                        'has_thumbnail' => !empty($thumbnail)
                    ]);

                    return [
                        'title' => $info['title'] ?? 'TikTok Video',
                        'duration' => $this->formatDuration($info['duration'] ?? 0),
                        'thumbnail' => $thumbnail,
                        'description' => $info['description'] ?? null,
                        'uploader' => $info['uploader'] ?? $info['creator'] ?? 'TikTok User',
                        'view_count' => $info['view_count'] ?? null,
                        'upload_date' => $info['upload_date'] ?? null,
                        'formats' => $info['formats'] ?? []
                    ];
                }
            }
            
            $lastError = $result['error'];
            
            // If timeout, stop trying
            if (isset($result['timeout']) && $result['timeout']) {
                Log::warning('TikTok video info detection timed out');
                break;
            }
        }

        throw new \Exception('Failed to get video info: ' . ($lastError ?: 'Deteksi video timeout. Silakan coba lagi.'));
    }

    public function downloadVideo(string $url, array $options = []): Download
    {
        $download = $this->createDownloadRecord($url);
        
        try {
            // Get video info first (with timeout protection)
            $info = $this->getVideoInfo($url);
            
            Log::info('TikTok video info retrieved', [
                'title' => $info['title'],
                'has_thumbnail' => !empty($info['thumbnail'])
            ]);

            // Download thumbnail locally
            $localThumbnail = null;
            if (!empty($info['thumbnail'])) {
                $localThumbnail = $this->downloadThumbnailLocally($info['thumbnail'], $url);
                Log::info('TikTok thumbnail download result', [
                    'original_url' => $info['thumbnail'],
                    'local_path' => $localThumbnail
                ]);
            }

            $download->update([
                'title' => $info['title'],
                'thumbnail' => $localThumbnail ?: $info['thumbnail'],
                'duration' => $info['duration'],
                'metadata' => $info
            ]);

            Log::info('Download record updated', [
                'id' => $download->id,
                'has_local_thumbnail' => !empty($localThumbnail)
            ]);

            $this->updateDownloadStatus($download, 'downloading');

            $success = false;
            $lastError = null;

            // Build download commands (limit to 4 most effective methods)
            $downloadCommands = $this->buildDownloadCommands($url, $options);

            foreach ($downloadCommands as $index => $command) {
                Log::info("Trying TikTok download method " . ($index + 1) . "/" . count($downloadCommands));
                
                // Use executeDownloadCommand with extended timeout
                $result = $this->executeDownloadCommand($command);

                if ($result['success']) {
                    $filePath = $this->extractFilePath($result['output'], $options);
                    if ($filePath && file_exists($filePath)) {
                        $fileSize = filesize($filePath);
                        
                        // Ensure thumbnail is downloaded
                        if (!$localThumbnail && isset($info['thumbnail'])) {
                            $localThumbnail = $this->downloadThumbnailLocally($info['thumbnail'], $url);
                            if ($localThumbnail) {
                                $download->update(['thumbnail' => $localThumbnail]);
                            }
                        }
                        
                        $this->updateDownloadStatus($download, 'completed', [
                            'file_path' => $filePath,
                            'file_size' => $fileSize
                        ]);
                        $success = true;
                        break;
                    }
                }
                
                $lastError = $result['error'];
                
                // If timeout occurred, stop trying
                if (isset($result['timeout']) && $result['timeout']) {
                    Log::error('TikTok download timed out');
                    $this->updateDownloadStatus($download, 'failed', [
                        'error_message' => 'Download timeout. Video mungkin terlalu besar atau koneksi lambat.'
                    ]);
                    return $download;
                }
            }

            if (!$success) {
                $this->updateDownloadStatus($download, 'failed', [
                    'error_message' => $lastError ?: 'Semua metode download gagal. Coba lagi nanti.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('TikTok download failed', [
                'error' => $e->getMessage(),
                'url' => $url
            ]);
            $this->updateDownloadStatus($download, 'failed', [
                'error_message' => $e->getMessage()
            ]);
        }

        return $download;
    }

    protected function downloadThumbnailLocally(string $thumbnailUrl, string $originalUrl): ?string
    {
        try {
            // Extract video ID from URL for filename
            $videoId = $this->extractVideoId($originalUrl);
            if (!$videoId) {
                $videoId = 'thumb_' . time() . '_' . rand(1000, 9999);
            }

            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $parsedUrl = parse_url($thumbnailUrl);
            $pathInfo = pathinfo($parsedUrl['path'] ?? '');
            $extension = $pathInfo['extension'] ?? 'jpg';
            
            $extension = explode('?', $extension)[0];
            if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp'])) {
                $extension = 'jpg';
            }

            $fileName = 'tiktok_' . $videoId . '_' . time() . '.' . $extension;
            $filePath = $thumbnailDir . '/' . $fileName;

            Log::info('Attempting to download TikTok thumbnail', [
                'url' => substr($thumbnailUrl, 0, 100) . '...',
                'destination' => $fileName
            ]);

            // Method 1: Try with curl (with timeout)
            $command = [
                'curl',
                '-s',
                '-L',
                '--max-redirs', '5',
                '--max-time', '30',
                '--connect-timeout', '10',
                '-A', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                '-H', 'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                '-o', $filePath,
                $thumbnailUrl
            ];

            // Use short timeout for thumbnail
            $result = $this->executeCommand($command, self::TIMEOUT_THUMBNAIL);

            if ($result['success'] && file_exists($filePath) && filesize($filePath) > 100) {
                Log::info('TikTok thumbnail downloaded successfully via curl', [
                    'file' => $fileName,
                    'size' => filesize($filePath)
                ]);
                return 'thumbnails/' . $fileName;
            }

            // Method 2: Try with HTTP client
            if (!$result['success'] || !file_exists($filePath) || filesize($filePath) <= 100) {
                @unlink($filePath);
                
                try {
                    $response = Http::timeout(30)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                            'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                        ])
                        ->get($thumbnailUrl);

                    if ($response->successful() && strlen($response->body()) > 100) {
                        file_put_contents($filePath, $response->body());
                        Log::info('TikTok thumbnail downloaded via HTTP client', [
                            'file' => $fileName,
                            'size' => filesize($filePath)
                        ]);
                        return 'thumbnails/' . $fileName;
                    }
                } catch (\Exception $e) {
                    Log::warning('TikTok HTTP client download failed', ['error' => $e->getMessage()]);
                }
            }

            Log::warning('TikTok thumbnail download failed', [
                'file_exists' => file_exists($filePath),
                'file_size' => file_exists($filePath) ? filesize($filePath) : 0
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download TikTok thumbnail', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    protected function extractVideoId(string $url): ?string
    {
        // TikTok URL patterns:
        // https://www.tiktok.com/@username/video/1234567890
        // https://vt.tiktok.com/ZS1234567/
        // https://vm.tiktok.com/ZS1234567/
        
        if (preg_match('/\/video\/(\d+)/', $url, $matches)) {
            return $matches[1];
        } elseif (preg_match('/\/(ZS[A-Za-z0-9]+)/', $url, $matches)) {
            return $matches[1];
        } elseif (preg_match('/tiktok\.com\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    protected function buildDownloadCommands(string $url, array $options = []): array
    {
        $commands = [];
        
        // Try with most effective impersonate targets only (limit to 3)
        foreach ($this->impersonateTargets as $target) {
            $commands[] = $this->buildCommand($url, $options, $target);
        }
        
        // Fallback without impersonate
        $commands[] = $this->buildCommand($url, $options, null);
        
        return $commands;
    }

    protected function buildCommand(string $url, array $options = [], ?string $impersonateTarget = null): array
    {
        $quality = $options['quality'] ?? $this->getSetting('quality', 'best');
        $format = $options['format'] ?? $this->getSetting('format', 'mp4');
        $audioOnly = $options['audio_only'] ?? $this->getSetting('audio_only', false);
        $embedMetadata = $options['embed_metadata'] ?? $this->getSetting('embed_metadata', true);

        if (in_array(strtolower($format), $this->audioFormats)) {
            $audioOnly = true;
        }

        $command = ['yt-dlp'];

        // Timeout and retry settings
        $command[] = '--socket-timeout';
        $command[] = '30';
        $command[] = '--retries';
        $command[] = '3';

        // Add impersonation if target is provided
        if ($impersonateTarget) {
            $command[] = '--impersonate';
            $command[] = $impersonateTarget;
        } else {
            // Use user agent if no impersonate
            $command[] = '--user-agent';
            $command[] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        }

        // Force write thumbnail for preview
        $command[] = '--write-thumbnail';
        $command[] = '--convert-thumbnails';
        $command[] = 'jpg';

        $command[] = '-o';
        if ($audioOnly) {
            $command[] = $this->downloadPath . '/%(title)s.' . $format;
        } else {
            $command[] = $this->downloadPath . '/%(title)s.%(ext)s';
        }

        if ($audioOnly) {
            $command[] = '--extract-audio';
            $command[] = '--audio-format';
            $command[] = $format;
            $command[] = '--audio-quality';
            $command[] = '0';

            if (strtolower($format) === 'mp3') {
                $command[] = '--postprocessor-args';
                $command[] = 'ffmpeg:-b:a 320k';
            }

            $command[] = '--format';
            $command[] = 'bestaudio/best';
        } else {
            $command[] = '--format';
            $command[] = $this->getQualityFormat($quality);
        }

        if ($embedMetadata) {
            $command[] = '--embed-metadata';
            if (!$audioOnly) {
                $command[] = '--embed-thumbnail';
            } else {
                $command[] = '--embed-thumbnail';
                $command[] = '--ppa';
                $command[] = 'EmbedThumbnail+ffmpeg_o:-c:v mjpeg';
            }
        }

        $command[] = '--no-playlist';
        $command[] = '--ignore-errors';
        $command[] = '--no-warnings';

        $command[] = $url;

        return $command;
    }

    protected function getQualityFormat(string $quality): string
    {
        return match($quality) {
            'best' => 'best',
            'worst' => 'worst',
            default => 'best'
        };
    }

    protected function formatDuration(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    protected function extractFilePath(string $output, array $options = []): ?string
    {
        $lines = explode("\n", $output);
        $format = $options['format'] ?? 'mp4';
        $audioOnly = in_array(strtolower($format), $this->audioFormats);
        
        $destinations = [];
        foreach ($lines as $line) {
            if (strpos($line, '[download] Destination:') !== false) {
                $destinations[] = trim(str_replace('[download] Destination:', '', $line));
            }
            if (strpos($line, '[ExtractAudio] Destination:') !== false) {
                return trim(str_replace('[ExtractAudio] Destination:', '', $line));
            }
            if (strpos($line, '[ffmpeg] Destination:') !== false) {
                return trim(str_replace('[ffmpeg] Destination:', '', $line));
            }
        }
        
        if ($audioOnly && !empty($destinations)) {
            $lastDestination = end($destinations);
            $pathInfo = pathinfo($lastDestination);
            $expectedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $format;
            
            if (file_exists($expectedPath)) {
                return $expectedPath;
            }
        }
        
        return !empty($destinations) ? end($destinations) : null;
    }

    public function getAvailableFormats(string $url): array
    {
        // Try with most effective impersonate targets
        foreach ($this->impersonateTargets as $target) {
            $command = [
                'yt-dlp',
                '--list-formats',
                '--socket-timeout', '30',
                '--impersonate', $target,
                $url
            ];

            // Use info timeout
            $result = $this->executeInfoCommand($command);

            if ($result['success']) {
                return $this->parseFormats($result['output']);
            }
            
            // Stop if timeout
            if (isset($result['timeout']) && $result['timeout']) {
                break;
            }
        }

        // Fallback without impersonate
        $command = [
            'yt-dlp',
            '--list-formats',
            '--socket-timeout', '30',
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            $url
        ];

        $result = $this->executeInfoCommand($command);

        if ($result['success']) {
            return $this->parseFormats($result['output']);
        }

        return [];
    }

    protected function parseFormats(string $output): array
    {
        $formats = [];
        $lines = explode("\n", $output);

        $maxHeight = 0;
        foreach ($lines as $line) {
            if (preg_match('/^(\d+)\s+(\w+)\s+(\d+x\d+|\w+)\s+(.+)/', $line, $matches)) {
                $resolution = $matches[3];
                if (preg_match('/(\d+)x(\d+)/', $resolution, $resMatches)) {
                    $height = (int)$resMatches[2];
                    $maxHeight = max($maxHeight, $height);
                }
                $formats[] = [
                    'id' => $matches[1],
                    'extension' => $matches[2],
                    'resolution' => $resolution,
                    'note' => trim($matches[4])
                ];
            }
        }

        $formats['max_height'] = $maxHeight;

        return $formats;
    }

    public function downloadAudio(string $url, string $format = 'mp3', array $options = []): Download
    {
        $options['audio_only'] = true;
        $options['format'] = $format;

        return $this->downloadVideo($url, $options);
    }

    public function getSupportedAudioFormats(): array
    {
        return $this->audioFormats;
    }

    /**
     * Get list of available impersonate targets
     */
    public function getAvailableImpersonateTargets(): array
    {
        $command = ['yt-dlp', '--list-impersonate-targets'];
        
        // Use short timeout for listing
        $result = $this->executeCommand($command, 30);
        
        if ($result['success']) {
            $lines = explode("\n", $result['output']);
            $targets = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && !str_starts_with($line, 'Available')) {
                    $targets[] = $line;
                }
            }
            
            return $targets;
        }
        
        return $this->impersonateTargets;
    }
}