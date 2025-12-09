<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set setting value
     */
    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'type' => 'string',
                'group' => 'general',
                'label' => $key
            ]);
        }

        Cache::forget("setting.{$key}");
        
        return true;
    }

    /**
     * Cast value to appropriate type
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Accessor: always return typed value when reading $setting->value
     */
    public function getValueAttribute($value)
    {
        return self::castValue($value, $this->type);
    }

    /**
     * Get settings by group
     */
    public static function getGroup($group)
    {
        return self::where('group', $group)->get()->mapWithKeys(function ($setting) {
            return [$setting->key => self::castValue($setting->value, $setting->type)];
        });
    }
}
