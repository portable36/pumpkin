<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'gateway',
        'tracking_number',
        'pickup_date',
        'delivery_date',
        'status',
        'weight',
        'dimensions',
        'cost',
        'notes',
        'gateway_response',
        'metadata',
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
        'delivery_date' => 'datetime',
        'weight' => 'float',
        'dimensions' => 'array',
        'cost' => 'float',
        'gateway_response' => 'json',
        'metadata' => 'json',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PICKUP_SCHEDULED = 'pickup_scheduled';
    const STATUS_PICKED_UP = 'picked_up';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_RETURNED = 'returned';

    const GATEWAY_STEADFAST = 'steadfast';
    const GATEWAY_PATHAO = 'pathao';

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PICKUP_SCHEDULED,
            self::STATUS_PICKED_UP,
            self::STATUS_IN_TRANSIT,
            self::STATUS_OUT_FOR_DELIVERY,
        ]);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    // Status update methods
    public function markAsPending()
    {
        $this->update(['status' => self::STATUS_PENDING]);
        return $this;
    }

    public function markAsPickupScheduled($date = null)
    {
        $this->update([
            'status' => self::STATUS_PICKUP_SCHEDULED,
            'pickup_date' => $date ?? now(),
        ]);
        return $this;
    }

    public function markAsPickedUp()
    {
        $this->update([
            'status' => self::STATUS_PICKED_UP,
            'pickup_date' => now(),
        ]);
        return $this;
    }

    public function markAsInTransit()
    {
        $this->update(['status' => self::STATUS_IN_TRANSIT]);
        return $this;
    }

    public function markAsOutForDelivery()
    {
        $this->update(['status' => self::STATUS_OUT_FOR_DELIVERY]);
        return $this;
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivery_date' => now(),
        ]);
        return $this;
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'notes' => $reason,
        ]);
        return $this;
    }

    public function markAsReturned()
    {
        $this->update(['status' => self::STATUS_RETURNED]);
        return $this;
    }

    /**
     * Calculate delivery estimate based on gateway
     */
    public function getEstimatedDeliveryDays(): int
    {
        return match($this->gateway) {
            self::GATEWAY_STEADFAST => 1,
            self::GATEWAY_PATHAO => 1,
            default => 2,
        };
    }

    /**
     * Check if shipment is in final state
     */
    public function isFinal(): bool
    {
        return in_array($this->status, [
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
            self::STATUS_FAILED,
            self::STATUS_RETURNED,
        ]);
    }

    /**
     * Check if shipment can be tracked
     */
    public function isTrackable(): bool
    {
        return !is_null($this->tracking_number) && !$this->isFinal();
    }
}
