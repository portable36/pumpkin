<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'email',
        'phone',
        'otp',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the OTP
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
