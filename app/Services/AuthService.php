<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\UserDevice;
use App\Models\UserSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class AuthService
{
    /**
     * Authenticate user with email/password
     */
    public function authenticate(string $email, string $password, ?string $deviceIdentifier = null): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $this->recordLoginAttempt($email, 'failed', 'Invalid credentials');
            return null;
        }

        if (!$user->is_active) {
            $this->recordLoginAttempt($email, 'failed', 'Account inactive');
            return null;
        }

        // Record successful login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => Request::ip(),
        ]);

        // Track device
        if ($deviceIdentifier) {
            $this->trackDevice($user, $deviceIdentifier);
        }

        $this->recordLoginAttempt($email, 'success');

        return $user;
    }

    /**
     * Record login attempt
     */
    private function recordLoginAttempt(string $email, string $status, ?string $reason = null): void
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'status' => $status,
            'reason' => $reason,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Track user device
     */
    private function trackDevice(User $user, string $deviceIdentifier): void
    {
        UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'device_identifier' => $deviceIdentifier],
            [
                'device_type' => $this->getDeviceType(),
                'os' => $this->getOS(),
                'last_ip' => Request::ip(),
                'last_user_agent' => Request::header('User-Agent'),
                'last_activity_at' => now(),
            ]
        );
    }

    /**
     * Get device type
     */
    private function getDeviceType(): string
    {
        $agent = Request::header('User-Agent');
        if (str_contains($agent, 'Mobile') || str_contains($agent, 'Android')) {
            return 'mobile';
        } elseif (str_contains($agent, 'Tablet') || str_contains($agent, 'iPad')) {
            return 'tablet';
        }
        return 'desktop';
    }

    /**
     * Get operating system
     */
    private function getOS(): string
    {
        $agent = Request::header('User-Agent');
        if (str_contains($agent, 'Windows')) return 'Windows';
        if (str_contains($agent, 'Mac')) return 'macOS';
        if (str_contains($agent, 'Linux')) return 'Linux';
        if (str_contains($agent, 'Android')) return 'Android';
        if (str_contains($agent, 'iPhone') || str_contains($agent, 'iPad')) return 'iOS';
        return 'Unknown';
    }

    /**
     * Check for multiple failed login attempts
     */
    public function checkRateLimit(string $email, int $attempts = 5, int $minutes = 15): bool
    {
        $failedAttempts = LoginAttempt::where('email', $email)
            ->where('status', 'failed')
            ->where('attempted_at', '>', now()->subMinutes($minutes))
            ->count();

        return $failedAttempts < $attempts;
    }

    /**
     * Verify email/phone OTP
     */
    public function verifyOTP(User $user, string $otp, string $type = 'email'): bool
    {
        $cache_key = "otp_{$user->id}_{$type}";
        $stored_otp = cache($cache_key);

        if (!$stored_otp || $stored_otp !== $otp) {
            return false;
        }

        if ($type === 'email') {
            $user->update(['email_verified_at' => now()]);
        } elseif ($type === 'phone') {
            $user->update(['phone_verified_at' => now()]);
        }

        cache()->forget($cache_key);

        return true;
    }

    /**
     * Generate OTP
     */
    public function generateOTP(User $user, string $type = 'email', int $minutes = 10): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        cache(["otp_{$user->id}_{$type}" => $otp], now()->addMinutes($minutes));
        return $otp;
    }
}
