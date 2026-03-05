<?php

namespace App\Models;

use App\Rules\{
    CheckValidEmail,
    CheckValidPhone,
    StrengthPassword
};
use Validator;

class PasswordReset extends Model
{
    protected $table = 'password_resets';

    public $timestamps = false;

    protected $fillable = [
        'email', 'phone', 'otp', 'token', 'created_at',
    ];

    /**
     * Store Validation
     *
     * @param  array  $data
     * @return mixed
     */
    public static function storeValidation($data = [])
    {
        $captchaRule = 'nullable';

        if (isRecaptchaActive()) {
            $data['gCaptcha'] = $data['g-recaptcha-response'] ?? null;
            $captchaRule = 'required|captcha';
        }

        // If both dial_code and phone are present, concatenate them for validation
        if (!empty($data['dial_code']) && !empty($data['phone'])) {
            $data['phone'] = $data['dial_code'] . $data['phone'];
        }

        if (array_key_exists('email', $data) ) {
            $rules = [
                'email' => ['required', 'email', 'exists:users', new CheckValidEmail()],
            ];
        } else {
            $rules = [
                'phone' => ['required', 'exists:users', new CheckValidPhone()],
            ];
        }

        $validator = Validator::make($data, $rules + ['gCaptcha' => $captchaRule]);

        return $validator;
    }

    /**
     * Password Validation
     *
     * @param  array  $data
     * @return mixed
     */
    protected static function passwordValidation($data = [])
    {
        $validator = Validator::make($data, [
            'password' => ['required', 'confirmed', new StrengthPassword()],
        ]);

        return $validator;
    }

    /**
     * store
     *
     * @param  array  $data
     * @return bool
     */
    public function storeOrUpdate($data = [])
    {
        return \DB::transaction(function () use ($data) {
            if (!empty($data['email'])) {
                parent::where('email', $data['email'])->delete();
            } 
            
            if (!empty($data['phone'])) {
                parent::where('phone', $data['phone'])->delete();
            }

            return parent::insert($data);
        });
    }

    /**
     * Check token existance
     *
     * @param  array  $data
     * @return bool
     */
    public function tokenExist($data)
    {
        if (parent::where('token', $data)->orWhere('otp', $data)->first()) {
            return true;
        }

        return false;
    }

    /**
     * Check if OTP has expired
     *
     * @param  string  $otp
     * @return bool
     */
    public function isOtpExpired($otp)
    {
        $passwordReset = parent::where('otp', $otp)->orWhere('token', $otp)->first();
        
        if (empty($passwordReset) || empty($passwordReset->created_at)) {
            return true; // Consider expired if not found or no created_at
        }

        $otpExpireIn = preference('otp_expire_in', 5); // Get expiration time in minutes
        $otpExpireInSeconds = $otpExpireIn * 60;
        
        // Convert created_at to timestamp
        $createdAt = is_string($passwordReset->created_at) 
            ? strtotime($passwordReset->created_at) 
            : (is_object($passwordReset->created_at) && method_exists($passwordReset->created_at, 'timestamp') 
                ? $passwordReset->created_at->timestamp 
                : strtotime($passwordReset->created_at));
        
        $currentTimestamp = time();
        $elapsedSeconds = $currentTimestamp - $createdAt;
        
        return $elapsedSeconds > $otpExpireInSeconds;
    }

    /**
     * Check if this specific reset record has expired
     * Uses the instance's created_at instead of doing a global lookup
     *
     * @return bool
     */
    public function isResetExpired(): bool
    {
        if (empty($this->created_at)) {
            return true; // Consider expired if no created_at
        }

        $otpExpireIn = preference('otp_expire_in', 5); // Get expiration time in minutes
        $otpExpireInSeconds = $otpExpireIn * 60;
        
        // Convert created_at to timestamp
        $createdAt = is_string($this->created_at) 
            ? strtotime($this->created_at) 
            : (is_object($this->created_at) && method_exists($this->created_at, 'timestamp') 
                ? $this->created_at->timestamp 
                : strtotime($this->created_at));
        
        $currentTimestamp = time();
        $elapsedSeconds = $currentTimestamp - $createdAt;
        
        return $elapsedSeconds > $otpExpireInSeconds;
    }

    /**
     * Update
     *
     * @param  array  $request
     * @param  int  $id
     * @return array
     */
    public function updatePassword($request = [], $id = null)
    {
        $data = ['status' => 'fail', 'message' => __('Something went wrong, please try again.')];
        $result = User::where('id', $id);
        if ($result->exists()) {
            $passwordReset = parent::where('token', $request['token'])->orWhere('otp', $request['token']);
            if (! $passwordReset->exists()) {
                return $data;
            }

            // Check if OTP has expired
            if ($this->isOtpExpired($request['token'])) {
                $data['message'] = __('OTP has expired. Please request a new OTP.');
                return $data;
            }

            $passwordReset->update(['token' => null, 'otp' => null]);

            $result->update(array_intersect_key($request, array_flip((array) ['password', 'updated_at'])));

            $data['status'] = 'success';
            $data['message'] = __('Password reset successfully');
        }

        return $data;
    }
}
