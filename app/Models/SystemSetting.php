<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'value' => 'json',
    ];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    public static function setValue($key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
