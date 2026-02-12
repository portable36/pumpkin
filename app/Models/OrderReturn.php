<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturn extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'reason',
        'description',
        'status',
        'refund_amount',
        'requested_at',
        'approved_at',
        'returned_at',
        'refunded_at',
        'admin_notes',
    ];

    protected $casts = [
        'refund_amount' => 'float',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'returned_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
