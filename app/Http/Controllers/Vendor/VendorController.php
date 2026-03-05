<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 *
 * @created 23-10-2021
 */

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateVendorRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use App\DataTables\UserActivityDataTable;
use App\Models\{
    Role,
    User,
    Vendor,
    TempOtp,
};
use App\Notifications\EmailPhoneChangeOtpNotification;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /**
     * Profile
     *
     * @return view
     */
    public function profile()
    {
        $userId = Auth::guard('user')->user()->id;
        $data['user'] = isset($userId) && ! empty($userId) ? User::with('vendors')->get()->where('id', $userId)->first() : null;
        $data['roleIds'] = $data['user']->roles()->pluck('id')->toArray();
        $data['roles'] = Role::getAll();
        $data['vendor'] = $data['user']->vendors->first();

        return view('vendor.profile.index', $data);
    }

    /**
     * Update Vendor
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $result = $this->checkExistence($id, 'users');
        if ($result['status'] === true) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:3|max:191',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $data['userData'] = $request->only('name');
            $data['userMetaData'] = $request->only('designation', 'description', 'facebook', 'twitter', 'instagram');
            (new user())->updateUser($data, $id);

            $response['status'] = 'success';
            $response['message'] = __('The :x has been successfully saved.', ['x' => strtolower(__('Vendor Info'))]);
        } else {
            $response['message'] = $result['message'];
        }

        $this->setSessionValue($response);
        if (isset($request->user_profile)) {
            return redirect()->back();
        }

        return redirect()->back();
    }

    /**
     * Update password
     *
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function updatePassword(Request $request, $id = null)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];
        if ($request->isMethod('post')) {
            $response = $this->checkExistence($id, 'users', ['getData' => true]);
            if ($response['status'] === true) {
                $validator = User::updatePasswordValidation($request->all());
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $request['user_name'] =  $response['data']->name;
                $request['email'] =  $response['data']->email;
                $request['raw_password'] = $request->password;
                $request['updated_at'] = date('Y-m-d H:i:s');
                $request['password'] = \Hash::make(trim($request->password));
                if ((new User())->updateUser($request->only('password', 'updated_at'), $id)) {
                    $data['status'] = 'success';
                    $data['message'] = __('Password update successfully.');
                } else {
                    $data['message'] = __('Nothing is updated.');
                }
            } else {
                $data['message'] = $response['message'];
            }
        }

        $this->setSessionValue($data);

        return redirect()->route('vendor-dashboard');
    }

    /**
     * logout operation.
     *
     * @return redirect login page view
     */
    public function logout()
    {
        $cookie = \Cookie::forget(explode('_', Auth::getRecallerName())[2]);

        $user = Auth::user();
        Auth::guard('user')->logout();

        if (isset($user)) {
            (new ActivityLogService())->userLogout('success', 'Logout successful', $user);
        }

        if ($user && isActive('Pos') && version_compare(moduleData('Pos')->get('version'), '2.0', '>=')) {
            expirePosSessionNow($user->id);
        }

        return redirect()->route('site.index')->withCookie($cookie);
    }

    /**
     * Update vendor
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Routing\Redirector
     */
    public function updateVendor(UpdateVendorRequest $request, $id)
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $result = $this->checkExistence($id, 'vendors');
        if ($result['status'] === true) {
            $request['phone'] = $request['dial_code'] . $request['phone'];

            try {
                DB::beginTransaction();
                $data['vendorData'] = $request->only('name', 'email', 'phone', 'formal_name', 'website');
                $data['vendorMetaData'] = $request->only('description', 'cover_photo', 'vendor_logo');
                (new Vendor())->updateVendor($data, $id);
                $response = $this->messageArray(__('The :x has been successfully saved.', ['x' => __('Vendor')]), 'success');
                DB::commit();
            } catch (\App\Exceptions\EmailException $th) {
                DB::rollBack();
                $response['status'] = 'fail';
                $response['message'] = __('Failed! Please configure your mail or template properly.');
            }
        } else {
            $response['message'] = $result['message'];
        }

        $this->setSessionValue($response);

        return redirect()->back();
    }

    /**
     * Show only vendor activity
     *
     * @return mixed
     */
    public function loginActivity(UserActivityDataTable $dataTable)
    {
        $logTypes = ['USER LOGIN', 'USER LOGOUT'];

        return $dataTable->render('vendor.profile.login_activity', ['logTypes' => $logTypes]);
    }

    /**
     * Send OTP for email/phone change
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendChangeOtp(Request $request)
    {
        $user = Auth::guard('user')->user();
        $type = $request->type; // 'email' or 'phone'
        $value = $request->value;
        $dialCode = $request->dial_code ?? '';

        // For phone, merge dial code with phone number if provided
        if ($type === 'phone' && !empty($dialCode)) {
            // Remove any existing + or dial code from the beginning of the value
            $value = ltrim($value, '+');
            // Remove dial code if it's already at the start
            if (strpos($value, $dialCode) === 0) {
                $value = substr($value, strlen($dialCode));
            }
            // Merge dial code with phone number (e.g., '+88' + '01751151078')
            $value = $dialCode . $value;
        }

        // Check if the new value is the same as current value
        $currentValue = $type === 'email' ? $user->email : $user->phone;
        if (strtolower(trim($value)) === strtolower(trim($currentValue))) {
            return response()->json([
                'status' => 'fail',
                'message' => __('The new :x must be different from your current :x.', ['x' => $type === 'email' ? __('email') : __('phone')])
            ], 422);
        }

        // Check cooldown period - how many days since last successful change
        $cooldownDays = (int) preference('email_phone_change_cooldown_days', 30);
        
        if ($cooldownDays > 0) {
            // Find the most recent verified OTP for this user and type
            $lastVerifiedOtp = TempOtp::where('user_id', $user->id)
                ->where('type', $type)
                ->whereNotNull('verified_at')
                ->orderBy('verified_at', 'desc')
                ->first();
            
            if ($lastVerifiedOtp) {
                $daysSinceLastChange = $lastVerifiedOtp->verified_at->diffInDays(now());
                if ($daysSinceLastChange < $cooldownDays) {
                    $remainingDays = $cooldownDays - $daysSinceLastChange;
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('You can change your :x again after :y days later.', [
                            'x' => $type === 'email' ? __('email') : __('phone'),
                            'y' => $remainingDays
                        ])
                    ], 422);
                }
            }
        }

        // Validate input first
        $field = $type === 'email' ? 'email' : 'phone';
        $rules = [
            'type' => 'required|in:email,phone',
            $field => $type === 'email' 
                ? 'required|email|unique:users,email'
                : 'required|string|unique:users,phone',
        ];
        $data = [
            'type' => $type,
            $field => $value,
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Check OTP attempts limit within 1 hour (BEFORE any deletion or OTP creation)
        $attemptsLimit = (int) preference('otp_attempts_limit', 5);
        
        // Ensure attempts limit is at least 1
        if ($attemptsLimit < 1) {
            $attemptsLimit = 5; // Default to 5 if invalid
        }
        
        // CRITICAL: Count attempts BEFORE deleting old OTPs
        // Count ALL attempts (both verified and unverified, for any email/phone value) within last hour
        // This prevents users from bypassing the limit by trying same or different email/phone values
        $oneHourAgo = now()->subHour();
        $attempts = TempOtp::where('user_id', $user->id)
            ->where('type', $type)
            ->whereNotNull('user_id')
            ->whereNotNull('type')
            ->where('created_at', '>=', $oneHourAgo)
            ->count();
        
        // Check if attempts limit exceeded (BEFORE any deletion)
        if ($attempts >= $attemptsLimit) {
            // Find the oldest attempt in the last hour to calculate remaining time
            $oldestAttempt = TempOtp::where('user_id', $user->id)
                ->where('type', $type)
                ->whereNotNull('user_id')
                ->whereNotNull('type')
                ->where('created_at', '>=', $oneHourAgo)
                ->orderBy('created_at', 'asc')
                ->first();
            
            if ($oldestAttempt) {
                $minutesElapsed = $oldestAttempt->created_at->diffInMinutes(now());
                $minutesRemaining = max(1, 60 - $minutesElapsed);
            } else {
                $minutesRemaining = 60;
            }
            
            return response()->json([
                'status' => 'fail',
                'message' => __('You have exceeded the maximum OTP attempts limit (:limit attempts per hour). Please try again after :minutes minutes.', [
                    'limit' => $attemptsLimit,
                    'minutes' => $minutesRemaining
                ])
            ], 422);
        }

        // Generate OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old unverified OTPs for this user, type, and SPECIFIC email/phone value
        // CRITICAL FIX: We need to preserve OTP records for accurate attempt counting
        // If we delete immediately, attempts with same email/phone won't be counted properly
        // Solution: Only delete OTPs that are older than the OTP expiration time
        // This way, recent attempts are preserved for counting, but old expired OTPs are cleaned up
        $otpExpireIn = (int) preference('otp_expire_in', 5);
        $otpExpireTime = now()->subMinutes($otpExpireIn);
        
        // Delete only expired unverified OTPs for the same email/phone
        // This preserves recent attempts for accurate counting while cleaning up old ones
        TempOtp::where('user_id', $user->id)
            ->where('type', $type)
            ->where($type, $value)
            ->whereNull('verified_at')
            ->where('created_at', '<', $otpExpireTime) // Only delete expired OTPs
            ->delete();

        // Store OTP with user_id and type
        $tempOtp = TempOtp::create([
            'user_id' => $user->id,
            'type' => $type,
            $type => $value,
            'otp' => $otp,
        ]);

        // Prepare request object for notification
        $notificationRequest = (object) [
            $type => $value,
            'otp' => $otp,
            'phone' => $type === 'phone' ? $value : $user->phone,
            'email' => $type === 'email' ? $value : $user->email,
        ];

        // Send notification
        try {
            $user->notify(new EmailPhoneChangeOtpNotification($notificationRequest, $type));
            
            // Get OTP expiration time from preference (default 5 minutes)
            $otpExpireIn = (int) preference('otp_expire_in', 5);
            
            return response()->json([
                'status' => 'success',
                'message' => __('OTP has been sent to your :x.', ['x' => $type === 'email' ? __('email') : __('phone')]),
                'temp_otp_id' => $tempOtp->id,
                'otp_expire_in' => $otpExpireIn, // Return expiration time in minutes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => __('Failed to send OTP. Please try again.'),
            ], 500);
        }
    }

    /**
     * Verify OTP and update email/phone
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyChangeOtp(Request $request)
    {
        $user = Auth::guard('user')->user();
        $type = $request->type;
        $otp = $request->otp;
        $tempOtpId = $request->temp_otp_id;

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:email,phone',
            'otp' => 'required|string|size:6',
            'temp_otp_id' => 'required|exists:temp_otps,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Get OTP expiration time from preference (default 5 minutes)
        $otpExpireIn = (int) preference('otp_expire_in', 5);

        try {
            DB::beginTransaction();

            // Find temp OTP with row lock to prevent race conditions with concurrent requests
            $tempOtp = TempOtp::where('id', $tempOtpId)
                ->where('user_id', $user->id)
                ->where('type', $type)
                ->where('otp', $otp)
                ->whereNull('verified_at')
                ->lockForUpdate()
                ->first();

            if (!$tempOtp) {
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'message' => __('Invalid or expired OTP.'),
                ], 422);
            }

            // Check if OTP has expired
            // Check expiration from updated_at if exists, otherwise use created_at
            $otpTimestamp = $tempOtp->updated_at ? $tempOtp->updated_at : $tempOtp->created_at;
            if ($otpTimestamp->diffInMinutes(now()) > $otpExpireIn) {
                DB::rollBack();
                return response()->json([
                    'status' => 'fail',
                    'message' => __('OTP has expired. Please request a new one.'),
                ], 422);
            }

            // Mark OTP as verified
            $tempOtp->update(['verified_at' => now()]);

            // Update user email/phone
            $updateData = [];
            if ($type === 'email') {
                $updateData['email'] = $tempOtp->email;
                $updateData['email_verified_at'] = now();
            } else {
                $updateData['phone'] = $tempOtp->phone;
                $updateData['phone_verified_at'] = now();
            }

            $user->update($updateData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('Your :x has been successfully updated.', ['x' => $type === 'email' ? __('email') : __('phone')]),
                'new_value' => $type === 'email' ? $tempOtp->email : $tempOtp->phone,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => __('Failed to update :x. Please try again.', ['x' => $type === 'email' ? __('email') : __('phone')]),
            ], 500);
        }
    }

    /**
     * Resend OTP
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendChangeOtp(Request $request)
    {
        $user = Auth::guard('user')->user();
        $tempOtpId = $request->temp_otp_id;

        $validator = Validator::make($request->all(), [
            'temp_otp_id' => 'required|exists:temp_otps,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $tempOtp = TempOtp::where('id', $tempOtpId)
            ->where('user_id', $user->id)
            ->first();

        if (!$tempOtp || $tempOtp->verified_at) {
            return response()->json([
                'status' => 'fail',
                'message' => __('Invalid request.'),
            ], 422);
        }

        // Generate new OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $tempOtp->update(['otp' => $otp, 'updated_at' => now()]);

        // Determine type from temp_otp record
        $type = $tempOtp->type ?? ($tempOtp->email ? 'email' : 'phone');
        $value = $tempOtp->email ?? $tempOtp->phone;

        // Prepare request object for notification
        $notificationRequest = (object) [
            $type => $value,
            'otp' => $otp,
            'phone' => $type === 'phone' ? $value : $user->phone,
            'email' => $type === 'email' ? $value : $user->email,
        ];

        // Get OTP expiration time from preference (default 5 minutes)
        $otpExpireIn = (int) preference('otp_expire_in', 5);

        // Send notification
        try {
            $user->notify(new EmailPhoneChangeOtpNotification($notificationRequest, $type));
            
            return response()->json([
                'status' => 'success',
                'message' => __('OTP has been resent to your :x.', ['x' => $type === 'email' ? __('email') : __('phone')]),
                'otp_expire_in' => $otpExpireIn, // Return expiration time in minutes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => __('Failed to resend OTP. Please try again.'),
            ], 500);
        }
    }
}
