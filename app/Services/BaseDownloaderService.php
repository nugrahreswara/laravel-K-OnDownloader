<?php

namespace App\Services;

use App\Models\Download;
use App\Models\Platform;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

abstract class BaseDownloaderService
{
    protected Platform $platform;
    protected string $downloadPath;
    protected array $defaultSettings = [];

    // Timeout constants untuk berbagai operasi (TETAP)
    protected const TIMEOUT_VIDEO_INFO = 60; // 1 menit untuk get info
    protected const TIMEOUT_DOWNLOAD = 3600; // 1 jam untuk download (Akan diganti dinamis)
    protected const TIMEOUT_THUMBNAIL = 30; // 30 detik untuk thumbnail
    
    // Konstanta baru untuk peningkatan kecepatan/keandalan
    protected const IDLE_TIMEOUT = 300; // 5 menit tanpa output (TETAP)
    protected const CONNECTION_TIMEOUT = 120; // 2 menit untuk koneksi awal
    protected const MAX_DOWNLOAD_TIMEOUT = 5400; // 90 menit (Batasan aman)

    public function __construct(Platform $platform)
    {
        $this->platform = $platform;
        $this->downloadPath = storage_path('app/downloads');
        $this->ensureDownloadDirectory();
    }

    abstract public function getVideoInfo(string $url): array;
    abstract public function downloadVideo(string $url, array $options = []): Download;
    abstract public function streamVideo(string $url, array $options = [], array $videoInfo = []): \Illuminate\Http\Response;
    abstract protected function buildCommand(string $url, array $options = []): array;

    protected function ensureDownloadDirectory(): void
    {
        if (!is_dir($this->downloadPath)) {
            mkdir($this->downloadPath, 0755, true);
        }
    }

    /**
     * Execute command with improved timeout handling
     */
    protected function executeCommand(array $command, int $timeout = null): array
    {
        // Gunakan timeout default yang lebih besar jika tidak ditentukan
        if ($timeout === null) {
            $timeout = self::TIMEOUT_DOWNLOAD; // Kembali menggunakan konstanta default lama
        }
        
        $process = new Process($command);
        $process->setTimeout($timeout);
        
        // Set idle timeout (timeout jika tidak ada output)
        $process->setIdleTimeout(self::IDLE_TIMEOUT); 
        // Note: Pastikan konstanta IDLE_TIMEOUT (300) masih ada di bagian atas class
        
        try {
            Log::info('Executing command', [
                'command' => implode(' ', array_map(function($arg) {
                    if (strpos($arg, 'cookie') !== false) {
                        return '[COOKIE_HIDDEN]';
                    }
                    return $arg;
                }, $command)),
                'timeout' => $timeout
            ]);
            
            // Run process dengan callback untuk monitoring
            $output = '';
            $errorOutput = '';
            
            $process->run(function ($type, $buffer) use (&$output, &$errorOutput) {
                if (Process::ERR === $type) {
                    $errorOutput .= $buffer;
                    Log::debug('Process error output', ['buffer' => trim($buffer)]);
                } else {
                    $output .= $buffer;
                    // Log::debug('Process output', ['buffer' => trim($buffer)]);
                }
            });
            
            $isSuccessful = $process->isSuccessful();
            
            Log::info('Command execution completed', [
                'success' => $isSuccessful,
                'exit_code' => $process->getExitCode(),
                'has_output' => !empty($output),
                'has_error' => !empty($errorOutput)
            ]);
            
            return [
                'success' => $isSuccessful,
                'output' => $output,
                'error' => $errorOutput,
                'exit_code' => $process->getExitCode()
            ];
        } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $e) {
            // Ini adalah exception paling umum untuk timeout, seharusnya kompatibel
            Log::error('Command execution timed out', [
                'command' => implode(' ', $command),
                'timeout' => $timeout,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'output' => $process->getOutput(),
                'error' => 'Process timed out after ' . $timeout . ' seconds. ' . $e->getMessage(),
                'exit_code' => -1,
                'timeout' => true
            ];
        } catch (\Exception $e) {
            // Catch-all untuk semua exception lainnya (termasuk idle timeout jika versi lama)
            Log::error('Command execution failed', [
                'command' => implode(' ', $command),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'output' => '',
                'error' => $e->getMessage(),
                'exit_code' => -1
            ];
        }
    }

    /**
     * Execute command for getting video info with shorter timeout
     */
    protected function executeInfoCommand(array $command): array
    {
        return $this->executeCommand($command, self::TIMEOUT_VIDEO_INFO);
    }

    /**
     * Execute command for downloading with dynamic/extended timeout
     */
    protected function executeDownloadCommand(array $command, ?int $customTimeout = null): array
    {
        $timeout = $customTimeout ?? $this->calculateTimeout(null);
        return $this->executeCommand($command, $timeout);
    }
    
    // ... (metode lainnya tetap sama) ...

    protected function getSetting(string $key, $default = null)
    {
        return $this->platform->getSetting($key, $default);
    }
    
    protected function parseVideoInfo(string $output): array
    {
        $info = [];

        // Try to parse as JSON first (for yt-dlp --dump-json output)
        $json = json_decode($output, true);
        if ($json !== null && isset($json['title'])) {
            $info['title'] = $json['title'];
            $info['duration_seconds'] = isset($json['duration']) ? (int)$json['duration'] : null;
            $info['thumbnail'] = $json['thumbnail'] ?? null;
            $info['estimated_file_size_mb'] = $this->getEstimatedSizeFromFormats($json['formats'] ?? []);
            $info['raw_json'] = $json; // Store full JSON for potential future use

            Log::debug('Parsed video info from JSON', [
                'title' => $info['title'],
                'duration' => $info['duration_seconds'],
                'size_mb' => $info['estimated_file_size_mb']
            ]);

            return $info;
        }

        // Fallback to line-based parsing
        $lines = explode("\n", $output);
        $duration = null;

        foreach ($lines as $line) {
            if (strpos($line, 'title:') !== false) {
                $info['title'] = trim(str_replace('title:', '', $line));
            } elseif (strpos($line, 'duration:') !== false) {
                $durationStr = trim(str_replace('duration:', '', $line));
                if (is_numeric($durationStr)) {
                    $duration = (int)$durationStr;
                }
            } elseif (strpos($line, 'thumbnail:') !== false) {
                $info['thumbnail'] = trim(str_replace('thumbnail:', '', $line));
            }
        }

        $info['duration_seconds'] = $duration;
        $info['estimated_file_size_mb'] = null; // No size estimation from line parsing

        Log::debug('Parsed video info from lines', [
            'title' => $info['title'] ?? null,
            'duration' => $info['duration_seconds']
        ]);

        return $info;
    }

    /**
     * Estimate file size from available formats (in MB)
     */
    protected function getEstimatedSizeFromFormats(array $formats): ?float
    {
        if (empty($formats)) {
            return null;
        }

        // Prioritize formats based on common preferences: mp4 > webm, higher resolution
        $preferredFormats = array_filter($formats, function ($format) {
            return isset($format['ext']) && in_array($format['ext'], ['mp4', 'webm']) &&
                   isset($format['height']) && $format['height'] >= 360;
        });

        if (empty($preferredFormats)) {
            $preferredFormats = $formats; // Fallback to all formats
        }

        // Sort by height descending, then by filesize if available
        usort($preferredFormats, function ($a, $b) {
            $heightA = $a['height'] ?? 0;
            $heightB = $b['height'] ?? 0;
            if ($heightA !== $heightB) {
                return $heightB <=> $heightA; // Higher resolution first
            }
            $sizeA = $a['filesize_approx'] ?? $a['filesize'] ?? 0;
            $sizeB = $b['filesize_approx'] ?? $b['filesize'] ?? 0;
            return $sizeB <=> $sizeA; // Larger size first (better quality)
        });

        $bestFormat = $preferredFormats[0] ?? null;
        if (!$bestFormat) {
            return null;
        }

        $fileSize = $bestFormat['filesize_approx'] ?? $bestFormat['filesize'] ?? null;
        if ($fileSize) {
            return round($fileSize / 1024 / 1024, 2); // Convert to MB
        }

        return null;
    }
    
    // ... (metode createDownloadRecord dan updateDownloadStatus tetap sama) ...
    
    protected function createDownloadRecord(string $url, array $info = []): Download
    {
        return Download::create([
            'url' => $url,
            'platform' => $this->platform->name,
            'title' => $info['title'] ?? null,
            'thumbnail' => $info['thumbnail'] ?? null,
            'duration' => $info['duration_seconds'] ?? ($info['duration'] ?? null), // Lebih suka durasi detik
            'status' => 'pending',
            'metadata' => $info
        ]);
    }

    protected function updateDownloadStatus(Download $download, string $status, array $data = []): void
    {
        $download->update(array_merge(['status' => $status], $data));
        
        Log::info('Download status updated', [
            'download_id' => $download->id,
            'status' => $status,
            'url' => $download->url
        ]);
    }

    // ... (metode getQualityOptions, getFormatOptions, downloadThumbnailLocally tetap sama) ...
    
    protected function getQualityOptions(): array
    {
        return [
            'best' => 'Best Quality',
            'worst' => 'Worst Quality',
            '720p' => '720p HD',
            '480p' => '480p SD',
            '360p' => '360p',
            '240p' => '240p'
        ];
    }

    protected function getFormatOptions(): array
    {
        return [
            'mp4' => 'MP4 Video',
            'webm' => 'WebM Video',
            'mp3' => 'MP3 Audio',
            'wav' => 'WAV Audio',
            'm4a' => 'M4A Audio'
        ];
    }
    
    protected function downloadThumbnailLocally(string $thumbnailUrl, string $originalUrl): ?string
    {
        try {
            // Extract post ID from original URL for filename
            $postId = null;
            if (preg_match('/\/p\/([A-Za-z0-9_-]+)/', $originalUrl, $matches)) {
                $postId = $matches[1];
            } elseif (preg_match('/\/reel\/([A-Za-z0-9_-]+)/', $originalUrl, $matches)) {
                $postId = $matches[1];
            }

            if (!$postId) {
                $postId = 'unknown_' . md5($originalUrl);
            }

            // Create thumbnails directory if it doesn't exist
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            // Generate filename
            $extension = pathinfo(parse_url($thumbnailUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $fileName = 'instagram_' . $postId . '_thumb.' . $extension;
            $filePath = $thumbnailDir . '/' . $fileName;

            // Download thumbnail using curl with timeout
            // **[Peningkatan Kecepatan: Menggunakan TIMEOUT_THUMBNAIL]**
            $command = [
                'curl',
                '-s',
                '--max-time', (string)self::TIMEOUT_THUMBNAIL, // Total timeout
                '--connect-timeout', '10', // Initial connection timeout
                '-A', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                '--referer', 'https://www.instagram.com/',
                '-o', $filePath,
                $thumbnailUrl
            ];

            // Gunakan TIMEOUT_THUMBNAIL
            $result = $this->executeCommand($command, self::TIMEOUT_THUMBNAIL);

            if ($result['success'] && file_exists($filePath) && filesize($filePath) > 0) {
                Log::info('Thumbnail downloaded successfully', [
                    'url' => $thumbnailUrl,
                    'path' => $filePath,
                    'size' => filesize($filePath)
                ]);
                
                // Return the public URL for the thumbnail
                return 'thumbnails/' . $fileName;
            }

            Log::warning('Thumbnail download failed', [
                'url' => $thumbnailUrl,
                'result' => $result
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download thumbnail locally', [
                'url' => $thumbnailUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Get recommended timeout based on video duration with optimized calculation
     */
    protected function calculateTimeout(?int $durationSeconds, ?int $estimatedFileSizeMB = null): int
    {
        // Base timeout untuk video pendek atau durasi tidak diketahui
        if (!$durationSeconds || $durationSeconds <= 60) {
            return 600; // 10 menit
        }

        // Asumsi kecepatan download 2 MB/s, Video H.264/AAC ~ 3-10 MB/menit per resolusi
        // Multiplier lebih agresif: 3x realtime (asumsi koneksi cepat)
        $baseMultiplier = 3; 
        $calculatedTimeout = $durationSeconds * $baseMultiplier;

        // Jika estimasi ukuran file tersedia, gunakan itu (lebih akurat)
        if ($estimatedFileSizeMB && $estimatedFileSizeMB > 0) {
            // Asumsikan kecepatan download 2 MB/s (16 Mbits/s)
            $downloadSpeedMBs = 2; 
            $downloadTimeSeconds = $estimatedFileSizeMB / $downloadSpeedMBs; 
            
            // Tambahkan buffer 50%
            $calculatedTimeout = max($calculatedTimeout, $downloadTimeSeconds * 1.5); 
        }

        // Terapkan batas: minimum 5 menit, maksimum 90 menit (MAX_DOWNLOAD_TIMEOUT)
        $calculatedTimeout = max(300, min(self::MAX_DOWNLOAD_TIMEOUT, $calculatedTimeout));

        // Tambahkan buffer terakhir untuk *overhead* (seperti konversi post-download)
        $calculatedTimeout = (int)($calculatedTimeout * 1.2); // 20% buffer

        Log::info('Optimized timeout calculation', [
            'duration' => $durationSeconds,
            'estimated_size_mb' => $estimatedFileSizeMB,
            'calculated_timeout' => $calculatedTimeout,
            'multiplier_used' => $baseMultiplier
        ]);

        return $calculatedTimeout;
    }
}