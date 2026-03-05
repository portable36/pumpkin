<?php

namespace App\Services;

use App\Models\OtpLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OtpLogService
{
    /**
     * Log OTP send attempt
     *
     * @param  string  $type
     * @param  string  $channel
     * @param  string|null  $provider
     * @param  mixed  $notifiable
     * @param  string|null  $otp
     * @param  string  $status
     * @param  string|null  $errorMessage
     * @param  string|null  $overrideEmail Override email (for email/phone change scenarios)
     * @param  string|null  $overridePhone Override phone (for email/phone change scenarios)
     * @return OtpLog
     */
    public static function log(
        string $type,
        string $channel,
        ?string $provider,
        $notifiable,
        ?string $otp = null,
        string $status = 'sent',
        ?string $errorMessage = null,
        ?string $overrideEmail = null,
        ?string $overridePhone = null
    ): OtpLog {
        try {
            $request = request();

            // Set email or phone based on channel - only one should be filled
            // Use override values if provided (for email/phone change scenarios)
            $email = null;
            $phone = null;
            
            if (strtolower($channel) === 'email') {
                $email = $overrideEmail ?? ($notifiable->email ?? null);
                $phone = null; // Ensure phone is null for email channel
            } elseif (strtolower($channel) === 'sms') {
                $phone = $overridePhone ?? ($notifiable->phone ?? null);
                // Ensure phone number has '+' prefix if it doesn't already
                if ($phone && !empty($phone) && !str_starts_with($phone, '+')) {
                    $phone = '+' . ltrim($phone, '+');
                }
                $email = null; // Ensure email is null for SMS channel
            } else {
                // Fallback: use email if available, otherwise phone
                $email = $overrideEmail ?? ($notifiable->email ?? null);
                $phone = $overridePhone ?? ($notifiable->phone ?? null);
                // Ensure phone number has '+' prefix if it doesn't already
                if ($phone && !empty($phone) && !str_starts_with($phone, '+')) {
                    $phone = '+' . ltrim($phone, '+');
                }
            }

            $logData = [
                'type' => $type,
                'channel' => $channel,
                'provider' => $provider,
                'browser_agent' => $request ? $request->userAgent() : null,
                'user_id' => $notifiable->id ?? null,
                'email' => $email,
                'phone' => $phone,
                'otp' => $otp, // Store OTP (can be hashed later for security)
                'status' => $status,
                'ip_address' => $request ? $request->ip() : null,
                'error_message' => $errorMessage,
            ];
            return OtpLog::create($logData);
        } catch (\Exception $e) {
            Log::error('Failed to log OTP: ' . $e->getMessage());
            // Return a dummy model to prevent errors
            return new OtpLog();
        }
    }

    /**
     * Update OTP log status
     *
     * @param  int  $otpLogId
     * @param  string  $status
     * @param  string|null  $errorMessage
     * @return bool
     */
    public static function updateStatus(int $otpLogId, string $status, ?string $errorMessage = null): bool
    {
        try {
            $updateData = ['status' => $status];
            if ($errorMessage) {
                $updateData['error_message'] = $errorMessage;
            }
            if ($status === 'verified') {
                $updateData['verified_at'] = now();
            }

            return OtpLog::where('id', $otpLogId)->update($updateData) > 0;
        } catch (\Exception $e) {
            Log::error('Failed to update OTP log status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark OTP as verified
     *
     * @param  string  $otp
     * @param  string  $type
     * @return bool
     */
    public static function markAsVerified(string $otp, string $type = 'password_reset'): bool
    {
        try {
            return OtpLog::where('otp', $otp)
                ->where('type', $type)
                ->where('status', 'sent')
                ->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                ]) > 0;
        } catch (\Exception $e) {
            Log::error('Failed to mark OTP as verified: ' . $e->getMessage());
            return false;
        }
    }
}

