<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'frame_slots',
        'print_type',
        'print_count',
        'price',
        'discount_price',
        'is_active',
        'is_featured',
        'features',
        'image_url',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2'
    ];

    /**
     * Get photo sessions using this package
     */
    public function photoSessions(): HasMany
    {
        return $this->hasMany(PhotoSession::class);
    }

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering packages
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get feature list as string
     */
    public function getFeaturesListAttribute(): string
    {
        if (!$this->features || !is_array($this->features)) {
            return '';
        }

        return implode(', ', $this->features);
    }

    /**
     * Get final price (with discount if available)
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->discount_price && $this->discount_price > 0 ? $this->discount_price : $this->price;
    }

    /**
     * Get formatted final price
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Get formatted discount price
     */
    public function getFormattedDiscountPriceAttribute(): string
    {
        return $this->discount_price ? 'Rp ' . number_format($this->discount_price, 0, ',', '.') : '';
    }

    /**
     * Check if package has discount
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price && $this->discount_price > 0 && $this->discount_price < $this->price;
    }

    /**
     * Scope for featured packages
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
