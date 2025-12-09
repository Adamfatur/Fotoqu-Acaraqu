<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subject',
        'content',
        'variables',
        'type',
        'status',
        'is_default',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for templates by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the default template for a given type.
     */
    public static function getDefault(string $type): ?self
    {
        return static::active()
            ->ofType($type)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Set as default template for its type.
     */
    public function setAsDefault(): void
    {
        // Unset other defaults for same type
        static::where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Render the template with given variables.
     */
    public function render(array $variables = []): array
    {
        $subject = $this->subject;
        $content = $this->content;

        // Replace variables in subject and content
        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value, $subject);
            $content = str_replace($placeholder, $value, $content);
        }

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }

    /**
     * Get available template variables for the type.
     */
    public static function getAvailableVariables(string $type): array
    {
        $commonVariables = [
            'customer_name' => 'Nama customer',
            'customer_email' => 'Email customer',
            'session_code' => 'Kode sesi',
            'date' => 'Tanggal',
            'time' => 'Waktu',
            'photobox_name' => 'Nama photobox',
            'company_name' => 'Nama perusahaan',
            'company_logo' => 'Logo perusahaan',
            'support_email' => 'Email support',
            'website_url' => 'URL website',
        ];

        $typeSpecificVariables = match ($type) {
            'frame_delivery' => [
                'frame_url' => 'URL download frame',
                'frame_expires' => 'Tanggal kadaluarsa link',
                'package_name' => 'Nama paket',
                'total_photos' => 'Jumlah total foto',
            ],
            'session_confirmation' => [
                'package_name' => 'Nama paket',
                'total_amount' => 'Total pembayaran',
                'payment_method' => 'Metode pembayaran',
            ],
            'payment_receipt' => [
                'transaction_id' => 'ID transaksi',
                'total_amount' => 'Total pembayaran',
                'payment_method' => 'Metode pembayaran',
                'payment_date' => 'Tanggal pembayaran',
            ],
            'welcome' => [
                'instructions' => 'Instruksi penggunaan',
            ],
            'reminder' => [
                'reminder_message' => 'Pesan pengingat',
                'action_required' => 'Tindakan yang diperlukan',
            ],
            default => [],
        };

        return array_merge($commonVariables, $typeSpecificVariables);
    }

    /**
     * Auto-generate slug from name.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
