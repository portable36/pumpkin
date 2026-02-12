<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'transaction_id',
        'status',
        'paid_at',
        'gateway_response',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
        'gateway_response' => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
