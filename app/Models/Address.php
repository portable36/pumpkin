<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'last_name',
        'phone',
        'email',
        'company',
        'street_address',
        'apartment',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
        'is_shipping_default',
        'is_billing_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_shipping_default' => 'boolean',
        'is_billing_default' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeShippingDefault($query)
    {
        return $query->where('is_shipping_default', true);
    }

    public function scopeBillingDefault($query)
    {
        return $query->where('is_billing_default', true);
    }

    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    // Methods
    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function fullAddress(): string
    {
        $parts = [
            $this->street_address,
            $this->apartment,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ];
        return implode(', ', array_filter($parts));
    }
}
