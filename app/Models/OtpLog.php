<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\ModelTrait;

class OtpLog extends Model
{
    use HasFactory;
    use ModelTrait;

    protected $table = 'otp_logs';

    protected $fillable = [
        'type',
        'channel',
        'provider',
        'browser_agent',
        'user_id',
        'email',
        'phone',
        'otp',
        'status',
        'ip_address',
        'error_message',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'otp',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
    ];

    /**
     * Get the user that owns the OTP log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
