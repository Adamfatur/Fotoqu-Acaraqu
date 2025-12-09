<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoboothEvent extends Model
{
    protected $fillable = [
        'name',
        'uuid',
        'package_id',
        'photobox_id',
        'status',
        'print_quota',
        'prints_used',
        'active_from',
        'active_until'
    ];

    protected $casts = [
        'active_from' => 'datetime',
        'active_until' => 'datetime',
        'print_quota' => 'integer',
        'prints_used' => 'integer'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function photobox()
    {
        return $this->belongsTo(Photobox::class);
    }

    public function photoSessions()
    {
        return $this->hasMany(PhotoSession::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('active_until')
                    ->orWhere('active_until', '>', now());
            });
    }

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
