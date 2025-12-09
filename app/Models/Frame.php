<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Frame extends Model
{
    protected $fillable = [
        'photo_session_id',
        'template_id',
        'slots',
        'filename',
        's3_path',
        's3_url',
        'presigned_url',
        'presigned_expires_at',
        'status',
        'is_printed',
        'printed_at',
        'email_sent_at',
        'email_count',
        'layout_data',
    ];

    protected $casts = [
        'presigned_expires_at' => 'datetime',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'email_count' => 'integer',
        'layout_data' => 'array',
    ];

    /**
     * Get the photo session that owns this frame.
     */
    public function photoSession(): BelongsTo
    {
        return $this->belongsTo(PhotoSession::class);
    }

    /**
     * Get the template used for this frame.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FrameTemplate::class, 'template_id');
    }

    /**
     * Get the full S3 URL for this frame.
     */
    public function getFullUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->s3_path);
    }

    /**
     * Get the preview URL for this frame (for display in gallery).
     */
    public function getPreviewUrlAttribute(): string
    {
        // If we have a valid presigned URL, use it
        if ($this->presigned_url && $this->isPresignedUrlValid()) {
            return $this->presigned_url;
        }
        
        // Otherwise use the serve route
        return route('photobox.serve-frame', ['frame' => $this->id]);
    }

    /**
     * Get the download URL for this frame.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('photobox.download-frame', ['frame' => $this->id]);
    }

    /**
     * Generate a new presigned URL for this frame.
     */
    public function generatePresignedUrl(int $expiresInDays = 7): string
    {
        // AWS S3 presigned URLs can't exceed 7 days
        $maxDays = min($expiresInDays, 7);
        $expiresAt = now()->addDays($maxDays);
        $url = Storage::disk('s3')->temporaryUrl($this->s3_path, $expiresAt);
        
        $this->update([
            'presigned_url' => $url,
            'presigned_expires_at' => $expiresAt,
        ]);

        return $url;
    }

    /**
     * Check if the presigned URL is still valid.
     */
    public function isPresignedUrlValid(): bool
    {
        return $this->presigned_expires_at && $this->presigned_expires_at->isFuture();
    }
}
