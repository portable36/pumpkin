<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'parent_order_id',
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'gateway',
        'status',
        'transaction_id',
        'external_id',
        'reference_id',
        'gateway_response',
        'failure_reason',
        'paid_at',
        'refunded_amount',
        'refunded_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'float',
        'refunded_amount' => 'float',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'gateway_response' => 'json',
        'metadata' => 'json',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    const GATEWAY_BKASH = 'bkash';
    const GATEWAY_SSL_COMMERZ = 'sslcommerz';
    const GATEWAY_STRIPE = 'stripe';
    const GATEWAY_PAYPAL = 'paypal';

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
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Methods
    public function markAsSuccessful($transactionId = null, $response = null)
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'gateway_response' => $response ?? $this->gateway_response,
            'paid_at' => now(),
        ]);

        return $this;
    }

    public function markAsFailed($reason = null, $response = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failure_reason' => $reason,
            'gateway_response' => $response ?? $this->gateway_response,
        ]);

        return $this;
    }

    public function markAsRefunded($amount = null)
    {
        $this->update([
            'status' => self::STATUS_REFUNDED,
            'refunded_amount' => $amount ?? $this->amount,
            'refunded_at' => now(),
        ]);

        return $this;
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            self::STATUS_SUCCESS => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_FAILED => 'danger',
            self::STATUS_REFUNDED => 'secondary',
            default => 'gray',
        };
    }
}
