<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorStaff extends Model
{
    protected $fillable = [
        'vendor_id',
        'user_id',
        'role',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'json',
        'is_active' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
