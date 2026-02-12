<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBankDetail extends Model
{
    protected $fillable = [
        'vendor_id',
        'account_holder_name',
        'bank_name',
        'account_number',
        'routing_number',
        'swift_code',
        'iban',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected $hidden = [
        'account_number',
        'routing_number',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
