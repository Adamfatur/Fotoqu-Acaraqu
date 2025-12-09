<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PhotoSession extends Model
{
    protected $fillable = [
        'session_code',
        'photobox_id',
        'user_id',
        'admin_id',
        'package_id',
        'customer_name',
        'customer_email',
        'frame_slots',
        'frame_design',
        'photo_filters',
        'total_price',
        'payment_status',
        'session_status',
        'approved_at',
        'started_at',
        'completed_at',
        'notes',
        'photobooth_event_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_price' => 'integer',
        'photo_filters' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($photoSession) {
            if (empty($photoSession->session_code)) {
                $photoSession->session_code = self::generateSessionCode();
            }
        });
    }

    /**
     * Get the photobox that owns this session.
     */
    public function photobox(): BelongsTo
    {
        return $this->belongsTo(Photobox::class);
    }

    /**
     * Get the user (customer) that owns this session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that created this session.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the package for this session.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the photos for this session.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Get the selected photos for this session.
     */
    public function selectedPhotos(): HasMany
    {
        return $this->hasMany(Photo::class)->where('is_selected', true);
    }

    /**
     * Get the frame for this session.
     */
    public function frame(): HasOne
    {
        return $this->hasOne(Frame::class);
    }

    /**
     * Get the generated animated GIF for this session (bonus content).
     */
    public function sessionGif(): HasOne
    {
        return $this->hasOne(\App\Models\SessionGif::class);
    }

    /**
     * Get the payment logs for this session.
     */
    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    /**
     * Get the activity logs for this session.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function photoboothEvent(): BelongsTo
    {
        return $this->belongsTo(PhotoboothEvent::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'session_code';
    }

    /**
     * Generate unique session code.
     */
    public static function generateSessionCode(): string
    {
        do {
            // Generate 10 character alphanumeric code (excluding confusing characters)
            $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Excluded: 0,1,I,O for clarity
            $code = 'FOTOQU-';
            for ($i = 0; $i < 10; $i++) {
                $code .= $characters[mt_rand(0, strlen($characters) - 1)];
            }
        } while (self::where('session_code', $code)->exists());

        return $code;
    }

    /**
     * Check if session can be approved.
     */
    public function canBeApproved(): bool
    {
        // Debug info
        \Log::debug('Session approval check', [
            'session_id' => $this->id,
            'session_code' => $this->session_code,
            'session_status' => $this->session_status,
            'payment_status' => $this->payment_status
        ]);

        // A session can be approved if it's in created state and payment is completed
        // Also allow free packages with zero price to be approved
        return ($this->session_status === 'created' && $this->payment_status === 'paid') ||
            ($this->session_status === 'created' && $this->total_price == 0);
    }

    /**
     * Check if session can be started.
     */
    public function canBeStarted(): bool
    {
        return $this->session_status === 'approved';
    }
}
