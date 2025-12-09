<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Photobox extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Get the photo sessions for this photobox.
     */
    public function photoSessions(): HasMany
    {
        return $this->hasMany(PhotoSession::class);
    }

    /**
     * Get active photo sessions for this photobox.
     */
    public function activePhotoSessions(): HasMany
    {
        // Return active/queued sessions with deterministic priority ordering:
        // 1) in_progress, 2) photo_selection, 3) processing, 4) approved
        // Then by started_at/approved_at and finally by id to stabilize selection
        return $this->hasMany(PhotoSession::class)
            ->whereIn('session_status', ['approved', 'in_progress', 'photo_selection', 'processing'])
            ->orderByRaw("CASE session_status 
                WHEN 'in_progress' THEN 1 
                WHEN 'photo_selection' THEN 2 
                WHEN 'processing' THEN 3 
                WHEN 'approved' THEN 4 
                ELSE 5 END")
            ->orderBy('started_at')
            ->orderBy('approved_at')
            ->orderBy('id');
    }

    /**
     * Access tokens for this photobox.
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(PhotoboxAccessToken::class);
    }

    /**
     * Latest valid (non-revoked, not expired) access token.
     */
    public function activeAccessToken(): HasOne
    {
        return $this->hasOne(PhotoboxAccessToken::class)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->latestOfMany();
    }

    /**
     * Check if photobox is available for new sessions.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'active' && !$this->activePhotoSessions()->exists();
    }
}
