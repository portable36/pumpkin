<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorReview extends Model
{
    protected $fillable = [
        'vendor_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'review',
        'is_verified_buyer',
        'is_approved',
    ];

    protected $casts = [
        'is_verified_buyer' => 'boolean',
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
