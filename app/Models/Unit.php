<?php

namespace App\Models;

class Unit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'abbr',
        'default',
        'status',
    ];

    /**
     * The default data.
     *
     * @var Unit|null
     */
    private static $defaultData;

    /**
     * Get the default unit.
     *
     * @return Unit|null
     */
    public static function getDefault()
    {
        return static::$defaultData ??= static::where('default', 'Yes')->first();
    }
}
