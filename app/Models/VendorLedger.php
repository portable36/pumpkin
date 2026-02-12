<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorLedger extends Model
{
    protected $fillable = [
        'vendor_id',
        'type',
        'amount',
        'reference_id',
        'reference_type',
        'description',
        'running_balance',
    ];

    protected $casts = [
        'amount' => 'float',
        'running_balance' => 'float',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }
}
