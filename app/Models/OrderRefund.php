<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRefund extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'reason',
        'status',
        'refund_method',
        'transaction_id',
        'requested_at',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
