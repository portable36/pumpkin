<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'vendor_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'shipping_method',
        'tracking_number',
        'delivery_address_id',
        'billing_address_id',
        'coupon_code',
        'notes',
        'admin_notes',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'ip_address',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'tax_amount' => 'float',
        'shipping_cost' => 'float',
        'total_amount' => 'float',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class);
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'delivery_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Methods
    public function markAsPaid(): bool
    {
        return $this->update(['payment_status' => 'paid', 'status' => 'processing']);
    }

    public function markAsShipped($trackingNumber = null): bool
    {
        return $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $trackingNumber,
        ]);
    }

    public function markAsDelivered(): bool
    {
        return $this->update([
            'status' => 'completed',
            'delivered_at' => now(),
        ]);
    }

    public function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
