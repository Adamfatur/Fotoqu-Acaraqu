<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'photo_session_id',
        'action',
        'description',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the user that performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the photo session associated with this activity.
     */
    public function photoSession(): BelongsTo
    {
        return $this->belongsTo(PhotoSession::class);
    }

    /**
     * Log an activity.
     */
    public static function log(string $action, string $description, array $data = [], ?PhotoSession $photoSession = null, ?User $user = null): self
    {
        return self::create([
            'user_id' => $user?->id ?? auth()->id(),
            'photo_session_id' => $photoSession?->id,
            'action' => $action,
            'description' => $description,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
