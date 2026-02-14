<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SystemSetting Model - Complete Platform Configuration
 * 
 * This model allows complete control of all platform features from the admin dashboard.
 * No code deployment needed - just update settings and the platform adapts.
 * 
 * Usage Examples:
 *  Setting::get('shipping.gateways.steadfast.enabled')
 *  Setting::set('commission.rate', 0.15)
 *  Setting::toggle('features.social_login')
 *  Setting::getByCategory('payment')
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'category', 'description'];
    protected $table = 'settings';
    public $timestamps = true;

    /**
     * Get a setting value by key with dotted notation support
     * Supports nested keys like: 'shipping.gateways.steadfast.enabled'
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            return self::castValue($setting->value, $setting->type);
        }
        
        return $default;
    }

    /**
     * Update or create a setting
     */
    public static function setValue($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Set a setting with type casting
     */
    public static function set($key, $value, $type = 'string')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => json_encode($value),
                'type' => $type,
                'category' => self::extractCategory($key),
            ]
        );
    }

    /**
     * Toggle a boolean setting
     */
    public static function toggle($key)
    {
        $current = self::get($key, false);
        $new = !$current;
        self::set($key, $new, 'boolean');
        return $new;
    }

    /**
     * Increment a numeric setting
     */
    public static function incrementValue($key, $amount = 1)
    {
        $current = (float) self::get($key, 0);
        self::set($key, $current + $amount, 'float');
    }

    /**
     * Get all settings by category
     */
    public static function getByCategory($category)
    {
        return static::where('category', $category)
            ->pluck('value', 'key')
            ->map(fn($v) => json_decode($v, true))
            ->toArray();
    }

    /**
     * Get all settings organized by category
     */
    public static function getAllSettings()
    {
        $settings = static::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }
        
        return $result;
    }

    /**
     * Reset a setting to default
     */
    public static function reset($key)
    {
        static::where('key', $key)->delete();
    }

    /**
     * Batch set multiple settings
     */
    public static function setMultiple(array $settings)
    {
        foreach ($settings as $key => $value) {
            self::set($key, $value['value'] ?? $value, $value['type'] ?? 'string');
        }
    }

    /**
     * Get settings for admin dashboard display
     */
    public static function getAdminData($category = null)
    {
        $query = static::query();
        
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->get()->map(function ($setting) {
            return [
                'key' => $setting->key,
                'value' => json_decode($setting->value, true),
                'type' => $setting->type,
                'category' => $setting->category,
                'description' => $setting->description,
            ];
        });
    }

    /**
     * Extract category from dotted key
     */
    private static function extractCategory($key)
    {
        return explode('.', $key)[0];
    }

    /**
     * Cast value to proper type
     */
    private static function castValue($value, $type)
    {
        $decoded = json_decode($value, true) ?? $value;
        
        return match($type) {
            'boolean' => (bool) $decoded,
            'integer' => (int) $decoded,
            'float' => (float) $decoded,
            'array' => (array) $decoded,
            'json' => json_decode($decoded, true),
            default => $decoded,
        };
    }
}
