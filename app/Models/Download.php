<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'url',
        'platform',
        'title',
        'thumbnail',
        'duration',
        'file_path',
        'file_size',
        'status',
        'error_message',
        'metadata',
        'folder'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform', 'name');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'downloading' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            default => 'secondary'
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return 'Unknown';
        
        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getPublicFilePathAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        $storagePath = storage_path('app/public/');
        $fullPath = $this->file_path;

        // If file_path is full path, convert to relative
        if (strpos($fullPath, $storagePath) === 0) {
            $relativePath = substr($fullPath, strlen($storagePath));
        } else {
            // Assume it's already relative
            $relativePath = $this->file_path;
            $fullPath = $storagePath . $relativePath;
        }

        if (!file_exists($fullPath)) {
            return null;
        }

        return $relativePath;
    }

    public function getVideoTypeAttribute(): string
    {
        if (!$this->file_path) {
            return 'video/mp4';
        }

        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'avi' => 'video/avi',
            'mov' => 'video/quicktime',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'video/mp4';
    }

    // Accessors for metadata fields
    public function getUploaderAttribute(): ?string
    {
        return $this->metadata['uploader'] ?? null;
    }

    public function getViewCountAttribute(): ?string
    {
        return $this->metadata['view_count'] ?? null;
    }

    public function getLikeCountAttribute(): ?string
    {
        return $this->metadata['like_count'] ?? null;
    }

    public function getUploadDateAttribute(): ?string
    {
        return $this->metadata['upload_date'] ?? null;
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->metadata['categories'] ?? $this->metadata['category'] ?? null;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->metadata['description'] ?? null;
    }

    public function getSourceUrlAttribute(): ?string
    {
        return $this->metadata['webpage_url'] ?? $this->metadata['url'] ?? null;
    }

    public function getQualityAttribute(): ?string
    {
        return $this->metadata['quality'] ?? null;
    }

    public function getFormatAttribute(): ?string
    {
        return $this->metadata['format'] ?? 'MP4';
    }

    public function getResolutionAttribute(): ?string
    {
        return $this->metadata['resolution'] ?? null;
    }

    public function getAudioFormatAttribute(): ?string
    {
        return $this->metadata['audio_format'] ?? 'Stereo';
    }

    public function getBitrateAttribute(): ?string
    {
        return $this->metadata['bitrate'] ?? null;
    }



    public function getProgressAttribute(): ?int
    {
        return $this->attributes['progress'] ?? null;
    }

    public function getDownloadedSizeAttribute(): ?string
    {
        return $this->attributes['downloaded_size'] ?? null;
    }

    public function getSpeedAttribute(): ?string
    {
        return $this->attributes['speed'] ?? null;
    }

    public function getEtaAttribute(): ?string
    {
        return $this->attributes['eta'] ?? null;
    }

    public function getRemainingSpaceAttribute(): ?string
    {
        return $this->attributes['remaining_space'] ?? null;
    }

    public function getDeviceAttribute(): ?string
    {
        return $this->attributes['device'] ?? 'Desktop';
    }

    public function getConnectionTypeAttribute(): ?string
    {
        return $this->attributes['connection_type'] ?? 'WiFi';
    }

    public function getServerLocationAttribute(): ?string
    {
        return $this->attributes['server_location'] ?? 'Indonesia';
    }

    public function getAppVersionAttribute(): ?string
    {
        return $this->attributes['app_version'] ?? 'v2.1.0';
    }
}
