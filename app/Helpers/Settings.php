<?php

namespace App\Helpers;

use App\Models\Setting;

class Settings
{
    public static function all()
    {
        return cache()->remember('settings.all', 3600, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $key, $default = null)
    {
        $all = static::all();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        return $default;
    }

    public static function clearCache()
    {
        cache()->forget('settings.all');
    }

    // Instance wrappers for facade compatibility
    public function getAll()
    {
        return static::all();
    }

    public function getValue(string $key, $default = null)
    {
        return static::get($key, $default);
    }

    public function clear()
    {
        static::clearCache();
    }
}
