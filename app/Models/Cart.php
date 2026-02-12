<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_code',
        'is_abandoned',
        'abandoned_at',
        'expires_at',
    ];

    protected $casts = [
        'is_abandoned' => 'boolean',
        'abandoned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->nullable();
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_abandoned', false)
            ->where('expires_at', '>', now());
    }

    public function scopeAbandoned($query)
    {
        return $query->where('is_abandoned', true);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum('total_price');
    }

    public function getTotalAttribute(): float
    {
        return $this->getSubtotalAttribute();
    }
}
