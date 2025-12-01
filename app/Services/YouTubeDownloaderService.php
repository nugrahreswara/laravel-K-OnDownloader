<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;
// Asumsi Log/Cache sudah diimpor jika Anda menggunakannya
use Illuminate\Support\Facades\Log;

class YouTubeDownloaderService extends BaseDownloaderService
{
    protected array $defaultSettings = [
        // Mengubah default ke 360p untuk kecepatan
        'quality' => '360p', 
        'format' => 'mp4',
        'audio_only' => false,
        'subtitles' => false,
        'embed_metadata' => false // Mematikan metadata untuk post-processing yang lebih cepat
    ];

    // Supported audio formats
    protected array $audioFormats = ['mp3', 'm4a'];

    // Konstanta baru untuk membatasi unduhan maksimum 30 detik
    protected const MAX_DOWNLOAD_TIME = 30; 

    public function getVideoInfo(string $url): array
    {
        // ... (Metode getVideoInfo tetap sama, karena hanya mengambil metadata) ...
        $maxRetries = 3;
        // Mengubah baseTimeout dari 300 menjadi 60 (sesuai TIMEOUT_VIDEO_INFO)
        $baseTimeout = 60; 

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $command = [
                'yt-dlp',
                '--dump-json',
                '--no-download',
                '--retries', '3',
                '--fragment-retries', '3',
                '--socket-timeout', '30',
                '--user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                $url
            ];

            // Gunakan executeInfoCommand jika tersedia, atau executeCommand dengan timeout yang lebih kecil
            $result = method_exists($this, 'executeInfoCommand') 
                      ? $this->executeInfoCommand($command) 
                      : $this->executeCommand($command, $baseTimeout);

            if ($result['success']) {
                $info = json_decode($result['output'], true);
                
                if ($info && isset($info['title'])) {
                    return [
                        'title' => $info['title'] ?? 'Unknown',
                        'duration' => $this->formatDuration($info['duration'] ?? 0),
                        'thumbnail' => $info['thumbnail'] ?? null,
                        'duration_seconds' => $info['duration'] ?? 0, // Tambahkan durasi detik
                        'description' => $info['description'] ?? null,
                        'uploader' => $info['uploader'] ?? null,
                        'view_count' => $info['view_count'] ?? null,
                        'upload_date' => $info['upload_date'] ?? null,
                        'formats' => $info['formats'] ?? []
                    ];
                }
            }

            Log::warning("Video info attempt {$attempt} failed for URL: {$url}", ['error' => $result['error']]);

            if ($attempt < $maxRetries) {
                $delay = pow(2, $attempt - 1); 
                sleep($delay);
            }
        }

        // Parse specific yt-dlp errors for better messages
        $error = $result['error'] ?? 'Unknown error';
        if (strpos($error, 'Video unavailable') !== false) {
            throw new \Exception('The video is unavailable or has been deleted. Please try another URL.');
        } elseif (strpos($error, 'Private video') !== false || strpos($error, 'Sign in to confirm your age') !== false) {
            throw new \Exception('The video is private or age-restricted. Please check the video settings or try another URL.');
        } elseif (strpos($error, 'ERROR:') !== false) {
            // Extract the specific error message after "ERROR:"
            preg_match('/ERROR: \[youtube\] [^\s]+: (.+)/', $error, $matches);
            $specificError = $matches[1] ?? $error;
            throw new \Exception('Failed to fetch video info: ' . $specificError);
        }

        throw new \Exception('Failed to get video info after ' . $maxRetries . ' attempts: ' . $error);
    }

    public function downloadVideo(string $url, array $options = []): Download
    {
        $info = $this->getVideoInfo($url);
        // Pastikan createDownloadRecord menerima info
        $download = $this->createDownloadRecord($url, $info); 
        
        try {
            $this->updateDownloadStatus($download, 'downloading');

            $command = $this->buildCommand($url, $options);
            
            Log::info('Download command: ' . implode(' ', array_map(function($arg) {
                return strpos($arg, ' ') !== false ? '"' . $arg . '"' : $arg;
            }, $command)));
            
            // **[OPTIMASI KECEPATAN: Batasi Total Waktu Unduh 30 Detik]**
            // Gunakan executeDownloadCommand (atau executeCommand) dengan timeout 30 detik
            $result = method_exists($this, 'executeDownloadCommand')
                      ? $this->executeDownloadCommand($command, self::MAX_DOWNLOAD_TIME)
                      : $this->executeCommand($command, self::MAX_DOWNLOAD_TIME);


            if ($result['success']) {
                // ... (Logika update status sukses dan penemuan file) ...
                $format = $options['format'] ?? 'mp4';
                $filePath = $this->extractFilePath($result['output'], $options, $info);
                
                Log::info('Extracted file path: ' . ($filePath ?? 'NULL'));
                
                $fileSize = file_exists($filePath) ? filesize($filePath) : null;

                // Logika fallback penemuan file (dipertahankan)
                if (!$filePath || !$fileSize) {
                    if (strtolower($format) === 'mp4' && !in_array(strtolower($format), $this->audioFormats)) {
                        $title = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $info['title']);
                        $expectedPath = $this->downloadPath . '/' . $title . '.mp4';
                        if (file_exists($expectedPath)) {
                            $filePath = $expectedPath;
                            $fileSize = filesize($expectedPath);
                        }
                    }
                }

                $audioOnly = in_array(strtolower($format), $this->audioFormats);
                $videoType = $audioOnly ? 'audio/mpeg' : 'video/mp4';

                $this->updateDownloadStatus($download, 'completed', [
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'format' => $format,
                    'video_type' => $videoType
                ]);
            } else {
                $this->updateDownloadStatus($download, 'failed', [
                    'error_message' => "Download time exceeded 30 seconds or failed: " . $result['error']
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Download exception: ' . $e->getMessage());
            $this->updateDownloadStatus($download, 'failed', [
                'error_message' => $e->getMessage()
            ]);
        }

        return $download;
    }

    protected function buildCommand(string $url, array $options = []): array
    {
        $quality = $options['quality'] ?? $this->getSetting('quality', '360p'); // Default 360p
        $format = $options['format'] ?? $this->getSetting('format', 'mp4');
        $audioOnly = $options['audio_only'] ?? $this->getSetting('audio_only', false);
        $subtitles = $options['subtitles'] ?? $this->getSetting('subtitles', false);
        // **[OPTIMASI KECEPATAN: Matikan Metadata]**
        $embedMetadata = false; 

        if (in_array(strtolower($format), $this->audioFormats)) {
            $audioOnly = true;
        }

        $command = ['yt-dlp'];
        $command[] = '-o';
        
        if ($audioOnly) {
            $command[] = $this->downloadPath . '/%(title)s.' . $format;
        } else {
            if (strtolower($format) === 'mp4') {
                $command[] = $this->downloadPath . '/%(title)s.mp4';
            } else {
                $command[] = $this->downloadPath . '/%(title)s.%(ext)s';
            }
        }

        // Konfigurasi Audio
        if ($audioOnly) {
            $command[] = '--extract-audio';
            $command[] = '--audio-format';
            $command[] = $format;
            $command[] = '--audio-quality';
            $command[] = '0'; 
            $command[] = '--format';
            $command[] = 'bestaudio/best';
        } else {
            // **[OPTIMASI KECEPATAN: Format Sederhana & Tanpa Post-Processing Berat]**
            $command[] = '--format';
            
            // Fokus pada satu format stream (mp4) dan resolusi rendah
            if (in_array($quality, ['360p', '480p'])) {
                // Pilih langsung format tunggal yang sudah muxed (jika tersedia)
                $command[] = $quality . '/best[ext=mp4]/best'; 
            } else {
                // Gunakan format merge, tapi dengan resolusi terbatas
                $command[] = $this->getQualityFormat($quality);
            }
            
            // **HILANGKAN MERGE (jika memungkinkan)**
            // Jika kita memilih format stream tunggal di atas, ini bisa lebih cepat.
            // Namun, karena YouTube memisahkan video/audio, merge tetap diperlukan untuk MP4 video.
            // Biarkan merge, tapi gunakan format kualitas terendah yang diminta.
            $command[] = '--merge-output-format';
            $command[] = 'mp4';
            
            // Post-processing ringan (copy) untuk kecepatan
            $command[] = '--postprocessor-args';
            $command[] = 'ffmpeg:-c:v copy -c:a aac';
        }

        // Nonaktifkan fitur yang memakan waktu
        $command[] = '--no-warnings';
        $command[] = '--no-playlist';
        $command[] = '--ignore-errors';
        $command[] = '--skip-unavailable-fragments'; // Melewati fragmen yang hilang

        $command[] = '--user-agent';
        $command[] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        
        // Atur retries rendah untuk proses download (agar cepat gagal)
        $command[] = '--retries';
        $command[] = '3';
        $command[] = '--fragment-retries';
        $command[] = '3';
        $command[] = '--socket-timeout';
        $command[] = '15'; // Socket timeout yang lebih rendah (15 detik)

        $command[] = $url;

        return $command;
    }

    protected function getQualityFormat(string $quality): string
    {
        // Pilihan format dimodifikasi untuk memprioritaskan kualitas rendah dan merge cepat
        return match($quality) {
            // Prioritas format MP4/M4A yang cepat di-merge untuk kecepatan
            'best' => 'bestvideo[height<=720][ext=mp4]+bestaudio[ext=m4a]/best[height<=720]',
            '1080p' => 'bestvideo[height<=1080][ext=mp4]+bestaudio[ext=m4a]/best[height<=1080]',
            '720p' => 'bestvideo[height<=720][ext=mp4]+bestaudio[ext=m4a]/best[height<=720]',
            '480p' => 'bestvideo[height<=480][ext=mp4]+bestaudio[ext=m4a]/best[height<=480]',
            '360p' => 'bestvideo[height<=360][ext=mp4]+bestaudio[ext=m4a]/best[height<=360]',
            '240p' => 'bestvideo[height<=240][ext=mp4]+bestaudio[ext=m4a]/best[height<=240]',
            'worst' => 'worstvideo+worstaudio/worst',
            default => 'bestvideo[height<=360][ext=mp4]+bestaudio[ext=m4a]/best[height<=360]' // Default ke 360p
        };
    }


        protected function formatDuration(int $seconds): string
        {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $seconds = $seconds % 60;
            
            if ($hours > 0) {
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
            
            return sprintf('%02d:%02d', $minutes, $seconds);
        }

        protected function extractFilePath(string $output, array $options = [], array $info = []): ?string
        {
            $lines = explode("\n", $output);
            $format = $options['format'] ?? 'mp4';
            $audioOnly = in_array(strtolower($format), $this->audioFormats);

            // Check for skipped download (already exists)
            if (strpos($output, 'has already been downloaded') !== false || strpos($output, 'already downloaded') !== false) {
                \Log::info('Download skipped - file already exists, constructing expected path');
                
                // Construct expected filename from title
                if (!empty($info['title'])) {
                    $title = $info['title'];
                    $expectedExt = $audioOnly ? $format : 'mp4';
                    $expectedFilename = $this->downloadPath . '/' . $title . '.' . $expectedExt;
                    
                    // Handle special characters in title (yt-dlp preserves them)
                    if (file_exists($expectedFilename)) {
                        \Log::info('Found existing file via title construction: ' . $expectedFilename);
                        return $expectedFilename;
                    }
                    
                    // Fallback: sanitize title for common patterns
                    $sanitizedTitle = preg_replace('/[<>:"/\\|?*]/', '_', $title); // Windows invalid chars
                    $sanitizedPath = $this->downloadPath . '/' . $sanitizedTitle . '.' . $expectedExt;
                    if (file_exists($sanitizedPath)) {
                        \Log::info('Found existing file via sanitized title: ' . $sanitizedPath);
                        return $sanitizedPath;
                    }
                    
                    // Scan directory for matching title prefix
                    $pattern = $this->downloadPath . '/' . preg_quote(substr($title, 0, 50), '/') . '*.' . $expectedExt;
                    $files = glob($pattern);
                    if (!empty($files)) {
                        // Return the most recent matching file
                        usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });
                        $candidate = $files[0];
                        \Log::info('Found existing file via glob: ' . $candidate);
                        return $candidate;
                    }
                }
            }

            // Priority 1: Cari [Merger] output untuk MP4 (PALING PENTING)
            foreach ($lines as $line) {
                if (strpos($line, '[Merger] Merging formats into') !== false) {
                    // Extract filename dari dalam quotes
                    if (preg_match('/"([^"]+)"/', $line, $matches)) {
                        $filename = $matches[1];
                        // Cek apakah path absolute atau relative
                        if (file_exists($filename)) {
                            return $filename;
                        }
                        // Jika relative, tambahkan download path
                        $fullPath = $this->downloadPath . '/' . $filename;
                        if (file_exists($fullPath)) {
                            return $fullPath;
                        }
                    }
                    // Fallback: extract tanpa quotes (case Instagram)
                    if (preg_match('/Merging formats into (.+)$/', $line, $matches)) {
                        $filename = trim($matches[1], ' "');
                        if (file_exists($filename)) {
                            return $filename;
                        }
                        $fullPath = $this->downloadPath . '/' . $filename;
                        if (file_exists($fullPath)) {
                            return $fullPath;
                        }
                    }
                }
            }

            // Priority 2: Cari [VideoConvertor] atau [ffmpeg] Destination
            foreach ($lines as $line) {
                if (strpos($line, '[ffmpeg] Destination:') !== false) {
                    $path = trim(str_replace('[ffmpeg] Destination:', '', $line));
                    if (file_exists($path)) {
                        return $path;
                    }
                }
                if (strpos($line, '[VideoConvertor] Not converting video file') !== false) {
                    preg_match('/"([^"]+)"/', $line, $matches);
                    if (isset($matches[1]) && file_exists($matches[1])) {
                        return $matches[1];
                    }
                }
            }

            // Priority 3: Cari [FixupM4a] atau post-processor output
            foreach ($lines as $line) {
                if (strpos($line, 'Destination:') !== false && strpos($line, '.mp4') !== false) {
                    if (preg_match('/"([^"]+\.mp4)"/', $line, $matches)) {
                        $path = $matches[1];
                        if (file_exists($path)) {
                            return $path;
                        }
                    }
                }
            }

            // Priority 4: ExtractAudio destination (untuk audio)
            if ($audioOnly) {
                foreach ($lines as $line) {
                    if (strpos($line, '[ExtractAudio] Destination:') !== false) {
                        $path = trim(str_replace('[ExtractAudio] Destination:', '', $line));
                        if (file_exists($path)) {
                            return $path;
                        }
                    }
                }
            }

            // Priority 5: Collect all download destinations
            $destinations = [];
            foreach ($lines as $line) {
                if (strpos($line, '[download] Destination:') !== false) {
                    $path = trim(str_replace('[download] Destination:', '', $line));
                    // Handle both absolute and relative paths
                    if (file_exists($path)) {
                        $destinations[] = $path;
                    } else {
                        $fullPath = $this->downloadPath . '/' . basename($path);
                        if (file_exists($fullPath)) {
                            $destinations[] = $fullPath;
                        } else {
                            $destinations[] = $path; // Keep original for later processing
                        }
                    }
                }
            }

            // Untuk video MP4, cari file .mp4 yang sebenarnya
            if (!$audioOnly && strtolower($format) === 'mp4' && !empty($destinations)) {
                foreach (array_reverse($destinations) as $dest) {
                    $pathInfo = pathinfo($dest);
                    
                    // Cek file dengan ekstensi .mp4
                    $expectedMp4Path = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.mp4';
                    if (file_exists($expectedMp4Path)) {
                        return $expectedMp4Path;
                    }
                    
                    // Cek file asli jika sudah .mp4
                    if (file_exists($dest) && strtolower($pathInfo['extension'] ?? '') === 'mp4') {
                        return $dest;
                    }
                }
            }

            // Fallback: Check destinations in reverse order
            foreach (array_reverse($destinations) as $dest) {
                if (file_exists($dest)) {
                    return $dest;
                }
            }

            // Last resort: Scan directory untuk file dengan nama yang mirip
            if (!empty($info['title'])) {
                $safeTitle = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $info['title']);
                $patterns = [
                    $this->downloadPath . '/' . $safeTitle . '.mp4',
                    $this->downloadPath . '/' . $safeTitle . ' *.mp4',
                    $this->downloadPath . '/*' . substr($safeTitle, 0, 20) . '*.mp4'
                ];
                
                foreach ($patterns as $pattern) {
                    $files = glob($pattern);
                    if (!empty($files)) {
                        // Return the most recent file
                        usort($files, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        return $files[0];
                    }
                }
            }

            // Ultimate fallback: Return last destination or null
            return !empty($destinations) ? end($destinations) : null;
        }

        public function getAvailableFormats(string $url): array
        {
            $command = [
                'yt-dlp',
                '--list-formats',
                $url
            ];

            $result = $this->executeCommand($command);

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

        /**
         * Download audio only in specified format
         */
        public function downloadAudio(string $url, string $format = 'mp3', array $options = []): Download
        {
            $options['audio_only'] = true;
            $options['format'] = $format;
            
            return $this->downloadVideo($url, $options);
        }

        /**
         * Get supported audio formats
         */
        public function getSupportedAudioFormats(): array
        {
            return $this->audioFormats;
        }
    }