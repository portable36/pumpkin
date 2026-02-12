<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorPayout extends Model
{
    protected $fillable = [
        'vendor_id',
        'amount',
        'period_start',
        'period_end',
        'status',
        'bank_account_id',
        'transaction_id',
        'requested_at',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }
}
