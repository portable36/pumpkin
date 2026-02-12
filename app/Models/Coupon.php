<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'applicable_categories',
        'applicable_products',
        'exclude_products',
        'exclude_sale_items',
    ];

    protected $casts = [
        'value' => 'float',
        'minimum_amount' => 'float',
        'maximum_discount' => 'float',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'applicable_categories' => 'json',
        'applicable_products' => 'json',
        'exclude_products' => 'json',
        'exclude_sale_items' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }

    public function isValid(): bool
    {
        return $this->is_active
            && now()->between($this->valid_from, $this->valid_until)
            && $this->used_count < $this->usage_limit;
    }
}
