<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class PhotoboxAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'photobox_id',
        'token',
        'expires_at',
        'created_by',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function photobox(): BelongsTo
    {
        return $this->belongsTo(Photobox::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValid(): bool
    {
        return !$this->revoked_at && $this->expires_at && $this->expires_at->isFuture();
    }
}
