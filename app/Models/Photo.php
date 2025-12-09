<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $fillable = [
        'photo_session_id',
        'sequence_number',
        'filename',
    'local_path',
        's3_path',
        's3_url',
        'file_size',
    'uploaded_at',
        'is_selected',
        'metadata',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
    'uploaded_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the photo session that owns this photo.
     */
    public function photoSession(): BelongsTo
    {
        return $this->belongsTo(PhotoSession::class);
    }

    /**
     * Get the full S3 URL for this photo.
     */
    public function getFullUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->s3_path);
    }

    /**
     * Get the public S3 URL for this photo.
     */
    public function getPublicUrl(): string
    {
        return Storage::disk('s3')->url($this->s3_path);
    }

    /**
     * Get the preview URL for this photo (for display in gallery).
     */
    public function getPreviewUrlAttribute(): string
    {
    return route('photobox.serve-photo', ['photo' => $this->id]);
    }

    /**
     * Get the thumbnail URL for this photo (same as preview for now).
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->preview_url;
    }

    /**
     * Get the download URL for this photo.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('photobox.serve-photo', ['photo' => $this->id]) . '?download=1';
    }

    /**
     * Get a temporary signed URL for this photo.
     */
    public function getSignedUrl(int $expiresInMinutes = 60): string
    {
        return Storage::disk('s3')->temporaryUrl($this->s3_path, now()->addMinutes($expiresInMinutes));
    }

    /**
     * Resolve a local-first URL to display the photo.
     * If local_path exists and file is available, return a route that serves it locally;
     * otherwise, serve from S3.
     */
    public function getLocalFirstUrlAttribute(): string
    {
        return route('photobox.serve-photo', ['photo' => $this->id]);
    }
}
