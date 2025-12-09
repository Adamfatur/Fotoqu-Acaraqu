<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    protected $fillable = [
        'photo_session_id',
        'admin_id',
        'amount',
        'payment_method',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the photo session that owns this payment log.
     */
    public function photoSession(): BelongsTo
    {
        return $this->belongsTo(PhotoSession::class);
    }

    /**
     * Get the admin that processed this payment.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
