<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShipment extends Model
{
    protected $fillable = [
        'order_id',
        'tracking_number',
        'courier_name',
        'status',
        'shipped_at',
        'delivered_at',
        'estimated_delivery_date',
        'carrier_response',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery_date' => 'datetime',
        'carrier_response' => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
