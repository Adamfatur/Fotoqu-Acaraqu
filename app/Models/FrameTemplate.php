<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class FrameTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slots',
        'template_path',
        'preview_path',
        'layout_config',
        'status',
        'is_default',
    'is_recommended',
        'background_color',
        'width',
        'height',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'is_default' => 'boolean',
    'is_recommended' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Get frames that use this template.
     */
    public function frames(): HasMany
    {
        return $this->hasMany(Frame::class, 'template_id');
    }

    /**
     * Get the full URL for the template image.
     */
    public function getTemplateUrlAttribute(): string
    {
        if (str_starts_with($this->template_path, 'http')) {
            return $this->template_path;
        }
        
        // Use url() with proper config to get correct base URL including port
        return url('storage/' . $this->template_path);
    }

    /**
     * Get the full URL for the preview image.
     */
    public function getPreviewUrlAttribute(): string
    {
        if (!$this->preview_path) {
            return url('images/default-template-preview.png');
        }

        if (str_starts_with($this->preview_path, 'http')) {
            return $this->preview_path;
        }
        
        // Use url() with proper config to get correct base URL including port
        return url('storage/' . $this->preview_path);
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for templates with specific slot count.
     */
    public function scopeForSlots($query, string $slots)
    {
        return $query->where('slots', $slots);
    }

    /**
     * Get the default template for given slots.
     */
    public static function getDefault(string $slots): ?self
    {
        return static::active()
            ->forSlots($slots)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Set as default template for its slot count.
     */
    public function setAsDefault(): void
    {
        // Unset other defaults for same slot count
        static::where('slots', $this->slots)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }
}
