<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookDownloaderService extends BaseDownloaderService
{
    protected array $defaultSettings = [
        'quality' => 'best',
        'format' => 'mp4',
        'audio_only' => false,
        'subtitles' => false,
        'embed_metadata' => true
    ];

    protected array $audioFormats = ['mp3', 'm4a', 'wav', 'opus', 'flac'];

    public function getVideoInfo(string $url): array
    {
        Log::info('Getting video info for Facebook URL', ['url' => $url]);

        // Normalize URL
        $url = $this->normalizeUrl($url);

        // Try multiple methods
        $methods = [
            'getVideoInfoFromYtDlp',
            'getVideoInfoFromApi',
        ];

        foreach ($methods as $method) {
            try {
                $info = $this->$method($url);
                if ($info && !empty($info['title'])) {
                    Log::info('Successfully got video info using ' . $method, [
                        'thumbnail' => $info['thumbnail'] ?? 'NO THUMBNAIL',
                        'title' => $info['title']
                    ]);
                    return $info;
                }
            } catch (\Exception $e) {
                Log::warning("Method {$method} failed", ['error' => $e->getMessage()]);
                continue;
            }
        }

        return $this->getBasicInfoFromUrl($url);
    }

    protected function normalizeUrl(string $url): string
    {
        // Remove mobile subdomain
        $url = str_replace('m.facebook.com', 'www.facebook.com', $url);
        
        // Remove unnecessary parameters
        $url = preg_replace('/[?&](mibextid|fs|sfnsn|__tn__|__xts__|paipv)=[^&]*/', '', $url);
        
        return rtrim($url, '?&');
    }

    protected function getVideoInfoFromYtDlp(string $url): ?array
    {
        $commands = [];

        // Enhanced base command
        $baseCommand = [
            'yt-dlp',
            '--dump-json',
            '--no-download',
            '--force-ipv4',
            '--no-cache-dir',
            '--socket-timeout', '30',
            '--retries', '3',
            '--fragment-retries', '3',
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            '--referer', 'https://www.facebook.com/',
            '--no-check-certificate',
            '--no-warnings'
        ];

        // Method 1: Try with cookies from browser
        $browsers = ['chrome', 'chromium', 'firefox', 'edge'];
        foreach ($browsers as $browser) {
            $command = array_merge($baseCommand, [
                '--cookies-from-browser', $browser,
                $url
            ]);
            $commands[] = $command;
        }

        // Method 2: Try with cookies file
        $cookiesFile = base_path('cookies.txt');
        if (file_exists($cookiesFile)) {
            $command = array_merge($baseCommand, [
                '--cookies', $cookiesFile,
                $url
            ]);
            $commands[] = $command;
        }

        // Method 3: Basic fallback without cookies
        $commands[] = array_merge($baseCommand, [$url]);

        foreach ($commands as $index => $command) {
            Log::debug("Trying info method " . ($index + 1), [
                'browser' => $command[array_search('--cookies-from-browser', $command) + 1] ?? 'none'
            ]);

            $result = $this->executeCommand($command, 60); // 60 seconds timeout

            if ($result['success']) {
                $info = json_decode($result['output'], true);
                if ($info && isset($info['title'])) {
                    $thumbnail = $this->extractBestThumbnail($info);

                    return [
                        'title' => $this->sanitizeTitle($info['title'] ?? 'Facebook Video'),
                        'duration' => $this->formatDuration($info['duration'] ?? 0),
                        'thumbnail' => $thumbnail,
                        'description' => $info['description'] ?? null,
                        'uploader' => $info['uploader'] ?? $info['uploader_id'] ?? 'Facebook User',
                        'view_count' => $info['view_count'] ?? null,
                        'upload_date' => $info['upload_date'] ?? null,
                        'formats' => $info['formats'] ?? []
                    ];
                }
            }
        }

        Log::warning('All Facebook video info attempts failed', ['url' => $url]);
        return null;
    }

    protected function extractBestThumbnail(array $info): ?string
    {
        // Try direct thumbnail
        if (isset($info['thumbnail']) && !empty($info['thumbnail'])) {
            return $info['thumbnail'];
        }

        // Try thumbnails array
        if (isset($info['thumbnails']) && is_array($info['thumbnails']) && !empty($info['thumbnails'])) {
            // Get highest quality thumbnail
            $thumbnails = $info['thumbnails'];
            usort($thumbnails, function($a, $b) {
                $aWidth = $a['width'] ?? 0;
                $bWidth = $b['width'] ?? 0;
                return $bWidth - $aWidth;
            });
            
            return $thumbnails[0]['url'] ?? null;
        }

        return null;
    }

    protected function sanitizeTitle(string $title): string
    {
        // Remove invalid filename characters
        $title = preg_replace('/[\/\\\:\*\?"<>\|]/', '', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);
        
        // Limit length
        if (strlen($title) > 200) {
            $title = substr($title, 0, 200);
        }
        
        return $title ?: 'Facebook Video';
    }

    protected function getVideoInfoFromApi(string $url): ?array
    {
        $videoId = $this->extractVideoId($url);
        if (!$videoId) {
            return null;
        }

        try {
            // Try Facebook Graph API for thumbnail
            $thumbnailUrl = "https://graph.facebook.com/v18.0/{$videoId}/picture?type=large";
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ])
                ->head($thumbnailUrl);
            
            if ($response->successful()) {
                return [
                    'title' => 'Facebook Video ' . substr($videoId, 0, 10),
                    'duration' => '00:00',
                    'thumbnail' => $thumbnailUrl,
                    'description' => null,
                    'uploader' => 'Facebook User',
                    'view_count' => null,
                    'upload_date' => null,
                    'formats' => []
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Facebook API method failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    protected function getBasicInfoFromUrl(string $url): array
    {
        $videoId = $this->extractVideoId($url);
        $thumbnailUrl = $videoId ? "https://graph.facebook.com/v18.0/{$videoId}/picture?type=large" : null;
        
        return [
            'title' => 'Facebook Video ' . ($videoId ? substr($videoId, 0, 10) : date('YmdHis')),
            'duration' => '00:00',
            'thumbnail' => $thumbnailUrl,
            'description' => null,
            'uploader' => 'Facebook User',
            'view_count' => null,
            'upload_date' => null,
            'formats' => []
        ];
    }

    protected function extractVideoId(string $url): ?string
    {
        // Try multiple patterns
        $patterns = [
            '/\/videos\/(\d+)/',           // /videos/123456
            '/\/watch\/\?v=(\d+)/',        // /watch/?v=123456
            '/\/reel\/(\d+)/',             // /reel/123456
            '/fbid=(\d+)/',                // fbid=123456
            '/\/story\.php\?story_fbid=(\d+)/', // story.php?story_fbid=123456
            '/\/(\d{15,})(?:\/|\?|$)/',    // Long numeric ID
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public function downloadVideo(string $url, array $options = []): Download
    {
        $download = $this->createDownloadRecord($url);
        
        try {
            // Normalize URL
            $url = $this->normalizeUrl($url);
            
            // Get video info first
            $info = $this->getVideoInfo($url);
            
            Log::info('Video info retrieved', [
                'title' => $info['title'],
                'thumbnail' => $info['thumbnail'] ?? 'NO THUMBNAIL'
            ]);

            // Download thumbnail locally
            $localThumbnail = null;
            if (!empty($info['thumbnail'])) {
                $localThumbnail = $this->downloadThumbnailLocally($info['thumbnail'], $url);
            }

            $download->update([
                'title' => $info['title'],
                'thumbnail' => $localThumbnail ?: $info['thumbnail'],
                'duration' => $info['duration'],
                'metadata' => $info
            ]);

            $this->updateDownloadStatus($download, 'downloading');

            $success = false;
            $lastError = null;

            // Try multiple download methods with improved commands
            $downloadMethods = [
                ['type' => 'chrome', 'description' => 'Chrome browser cookies'],
                ['type' => 'chromium', 'description' => 'Chromium browser cookies'],
                ['type' => 'firefox', 'description' => 'Firefox browser cookies'],
                ['type' => 'cookies_file', 'description' => 'Cookies from file'],
                ['type' => 'basic', 'description' => 'Basic without cookies'],
            ];

            foreach ($downloadMethods as $index => $method) {
                $attempt = $index + 1;
                
                Log::info("Starting Facebook download: {$method['description']}", [
                    'attempt' => $attempt,
                    'total' => count($downloadMethods)
                ]);

                $command = $this->buildDownloadCommand($url, $options, $method['type']);
                $result = $this->executeDownloadCommand($command, 600); // 10 minutes timeout

                if ($result['success']) {
                    $filePath = $this->extractFilePath($result['output'], $options);
                    
                    if ($filePath && file_exists($filePath) && filesize($filePath) > 1024) {
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
                        Log::info('Facebook download succeeded', [
                            'attempt' => $attempt,
                            'method' => $method['description'],
                            'file_size' => $this->formatBytes($fileSize)
                        ]);
                        break;
                    } else {
                        Log::warning("Download produced invalid file", [
                            'attempt' => $attempt,
                            'file_exists' => file_exists($filePath ?? ''),
                            'file_size' => file_exists($filePath ?? '') ? filesize($filePath) : 0
                        ]);
                    }
                } else {
                    $errorMsg = $result['error'] ?? 'Unknown error';
                    Log::error("Download attempt {$attempt} failed", [
                        'method' => $method['description'],
                        'error' => substr($errorMsg, 0, 500),
                        'timeout' => $result['timeout'] ?? false
                    ]);
                    $lastError = $errorMsg;
                }
            }

            if (!$success) {
                $errorMessage = $this->parseError($lastError) ?: 'Download failed after all attempts';
                Log::error('All Facebook download attempts failed', [
                    'url' => $url,
                    'error' => $errorMessage
                ]);
                $this->updateDownloadStatus($download, 'failed', [
                    'error_message' => $errorMessage
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Download exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $this->updateDownloadStatus($download, 'failed', [
                'error_message' => $e->getMessage()
            ]);
        }

        return $download;
    }

    protected function parseError(?string $error): ?string
    {
        if (!$error) return null;

        // Extract meaningful error messages
        if (strpos($error, 'Sign in to confirm') !== false) {
            return 'Video requires authentication. Please login to Facebook first.';
        }
        if (strpos($error, 'Video unavailable') !== false) {
            return 'Video is unavailable or has been removed.';
        }
        if (strpos($error, 'Private video') !== false) {
            return 'Video is private and cannot be downloaded.';
        }
        if (strpos($error, 'This video is not available') !== false) {
            return 'Video is not available in your region or has been deleted.';
        }
        if (strpos($error, 'No video formats found') !== false) {
            return 'Could not find downloadable video format.';
        }

        return substr($error, 0, 200);
    }

    protected function buildCommand(string $url, array $options = []): array
    {
        // Gunakan method buildDownloadCommand dengan tipe default
        return $this->buildDownloadCommand($url, $options, 'chrome');
    }

    protected function buildDownloadCommand(string $url, array $options = [], string $type = 'chrome'): array
    {
        $quality = $options['quality'] ?? $this->getSetting('quality', 'best');
        $format = $options['format'] ?? $this->getSetting('format', 'mp4');
        $audioOnly = $options['audio_only'] ?? $this->getSetting('audio_only', false);
        $subtitles = $options['subtitles'] ?? $this->getSetting('subtitles', false);
        $embedMetadata = $options['embed_metadata'] ?? $this->getSetting('embed_metadata', true);

        if (in_array(strtolower($format), $this->audioFormats)) {
            $audioOnly = true;
        }

        $command = [
            'yt-dlp',
            '--force-ipv4',
            '--no-cache-dir',
            '--socket-timeout', '30',
            '--retries', '5',
            '--fragment-retries', '5',
            '--concurrent-fragments', '4',
            '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            '--referer', 'https://www.facebook.com/',
            '--no-check-certificate',
            '--write-thumbnail',
            '--no-playlist',
        ];

        // Add cookie handling based on type
        if (in_array($type, ['chrome', 'chromium', 'firefox', 'edge'])) {
            $command[] = '--cookies-from-browser';
            $command[] = $type;
        } elseif ($type === 'cookies_file') {
            $cookiesFile = base_path('cookies.txt');
            if (file_exists($cookiesFile)) {
                $command[] = '--cookies';
                $command[] = $cookiesFile;
            }
        }

        // Output path
        $command[] = '-o';
        if ($audioOnly) {
            $command[] = $this->downloadPath . '/%(title).200B.' . $format;
        } else {
            $command[] = $this->downloadPath . '/%(title).200B.%(ext)s';
        }

        // Audio or video format
        if ($audioOnly) {
            $command = array_merge($command, [
                '--extract-audio',
                '--audio-format', $format,
                '--audio-quality', '0',
                '--format', 'bestaudio/best'
            ]);

            if (strtolower($format) === 'mp3') {
                $command[] = '--postprocessor-args';
                $command[] = 'ffmpeg:-b:a 320k';
            }
        } else {
            // Video format selection
            $formatSpec = $this->getQualityFormatSpec($quality);
            $command[] = '--format';
            $command[] = $formatSpec;
            
            // Merge formats if needed
            $command[] = '--merge-output-format';
            $command[] = 'mp4';
        }

        // Metadata and subtitles
        if ($embedMetadata) {
            $command[] = '--embed-metadata';
            $command[] = '--embed-thumbnail';
            if ($audioOnly) {
                $command[] = '--ppa';
                $command[] = 'EmbedThumbnail+ffmpeg_o:-c:v mjpeg -vf scale=320:320';
            }
        }

        if (!$audioOnly && $subtitles) {
            $command[] = '--write-subs';
            $command[] = '--write-auto-subs';
            $command[] = '--sub-lang';
            $command[] = 'en,id';
        }

        $command[] = $url;

        return $command;
    }

    protected function getQualityFormatSpec(string $quality): string
    {
        return match($quality) {
            'best' => 'bestvideo[ext=mp4][height<=1080]+bestaudio[ext=m4a]/bestvideo[ext=mp4]+bestaudio/best[ext=mp4]/best',
            '1080p' => 'bestvideo[ext=mp4][height<=1080]+bestaudio[ext=m4a]/best[height<=1080][ext=mp4]/best[height<=1080]',
            '720p' => 'bestvideo[ext=mp4][height<=720]+bestaudio[ext=m4a]/best[height<=720][ext=mp4]/best[height<=720]',
            '480p' => 'bestvideo[ext=mp4][height<=480]+bestaudio[ext=m4a]/best[height<=480][ext=mp4]/best[height<=480]',
            '360p' => 'best[height<=360][ext=mp4]/best[height<=360]',
            'worst' => 'worst[ext=mp4]/worst',
            default => 'best[ext=mp4]/best'
        };
    }

    protected function downloadThumbnailLocally(string $thumbnailUrl, string $originalUrl): ?string
    {
        try {
            $videoId = $this->extractVideoId($originalUrl) ?? 'fb_' . time();
            $thumbnailDir = storage_path('app/public/thumbnails');
            
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $extension = 'jpg';
            $fileName = 'facebook_' . $videoId . '_' . time() . '.' . $extension;
            $filePath = $thumbnailDir . '/' . $fileName;

            // Method 1: Use curl with better options
            $command = [
                'curl',
                '-L', // Follow redirects
                '-s', // Silent
                '--max-redirs', '5',
                '--max-time', '30',
                '--compressed',
                '-A', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                '-H', 'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                '-H', 'Accept-Language: en-US,en;q=0.9',
                '-H', 'Referer: https://www.facebook.com/',
                '-o', $filePath,
                $thumbnailUrl
            ];

            $result = $this->executeCommand($command, 30);

            if ($result['success'] && file_exists($filePath) && filesize($filePath) > 500) {
                Log::info('Thumbnail downloaded via curl', [
                    'size' => $this->formatBytes(filesize($filePath))
                ]);
                return 'thumbnails/' . $fileName;
            }

            // Method 2: HTTP client fallback
            @unlink($filePath);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                    'Referer' => 'https://www.facebook.com/',
                ])
                ->get($thumbnailUrl);

            if ($response->successful() && strlen($response->body()) > 500) {
                file_put_contents($filePath, $response->body());
                Log::info('Thumbnail downloaded via HTTP', [
                    'size' => $this->formatBytes(filesize($filePath))
                ]);
                return 'thumbnails/' . $fileName;
            }

            @unlink($filePath);
            return null;

        } catch (\Exception $e) {
            Log::error('Thumbnail download failed', [
                'url' => $thumbnailUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    protected function extractFilePath(string $output, array $options = []): ?string
    {
        $lines = explode("\n", $output);
        $format = $options['format'] ?? 'mp4';
        $audioOnly = in_array(strtolower($format), $this->audioFormats);
        
        // Look for final destination markers
        $markers = [
            '[ExtractAudio] Destination: ',
            '[VideoConvertor] Destination: ',
            '[ffmpeg] Destination: ',
            '[Merger] Merging formats into "',
            '[download] ',
        ];

        $lastDestination = null;
        
        foreach ($lines as $line) {
            foreach ($markers as $marker) {
                if (strpos($line, $marker) !== false) {
                    $path = trim(str_replace($marker, '', $line));
                    $path = str_replace('"', '', $path);
                    
                    // For download marker, extract the file path
                    if ($marker === '[download] ') {
                        if (preg_match('/([^"\s]+\.(mp4|mp3|m4a|wav|opus|flac))/', $line, $matches)) {
                            $path = $matches[1];
                        } else {
                            continue;
                        }
                    }
                    
                    if (file_exists($path) && filesize($path) > 1024) {
                        $lastDestination = $path;
                    }
                }
            }
        }

        // For audio conversion, check if converted file exists
        if ($audioOnly && $lastDestination) {
            $pathInfo = pathinfo($lastDestination);
            $convertedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $format;
            
            if (file_exists($convertedPath)) {
                return $convertedPath;
            }
        }

        return $lastDestination;
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    protected function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) return '00:00';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    protected function executeCommand(array $command, int $timeout = 60): array
    {
        try {
            $process = proc_open(
                $command,
                [
                    0 => ['pipe', 'r'],  // stdin
                    1 => ['pipe', 'w'],  // stdout
                    2 => ['pipe', 'w'],  // stderr
                ],
                $pipes
            );

            if (!is_resource($process)) {
                return [
                    'success' => false,
                    'output' => '',
                    'error' => 'Failed to create process',
                    'exit_code' => -1
                ];
            }

            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);

            $startTime = time();
            $output = '';
            $error = '';

            while (true) {
                $status = proc_get_status($process);
                
                if (!$status['running']) {
                    // Process finished
                    $output .= stream_get_contents($pipes[1]);
                    $error .= stream_get_contents($pipes[2]);
                    break;
                }

                if (time() - $startTime > $timeout) {
                    // Timeout
                    proc_terminate($process, 9);
                    fclose($pipes[0]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($process);
                    
                    return [
                        'success' => false,
                        'output' => $output,
                        'error' => 'Process timeout after ' . $timeout . ' seconds',
                        'exit_code' => -1,
                        'timeout' => true
                    ];
                }

                // Read output
                $output .= stream_get_contents($pipes[1]);
                $error .= stream_get_contents($pipes[2]);

                usleep(100000); // Sleep 0.1 second
            }

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            $exitCode = proc_close($process);

            return [
                'success' => $exitCode === 0,
                'output' => $output,
                'error' => $error,
                'exit_code' => $exitCode
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'output' => '',
                'error' => $e->getMessage(),
                'exit_code' => -1
            ];
        }
    }

    protected function executeDownloadCommand(array $command, int $timeout = 600): array
    {
        return $this->executeCommand($command, $timeout);
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

    public function getAvailableFormats(string $url): array
    {
        $url = $this->normalizeUrl($url);
        
        $command = [
            'yt-dlp',
            '--list-formats',
            '--cookies-from-browser', 'chrome',
            '--no-warnings',
            $url
        ];

        $result = $this->executeCommand($command, 60);

        if (!$result['success']) {
            return [];
        }

        $formats = [];
        $lines = explode("\n", $result['output']);
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
}   