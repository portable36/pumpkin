<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @contributor Md Abdur Rahaman Zihad <[zihad.techvill@gmail.com]>
 *
 * @created 20-05-2021
 *
 * @modified 30-05-2022
 */

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AuthUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\{
    User,
    PasswordReset,
};
use App\Notifications\ResetPasswordNotification;
use App\Notifications\UserPasswordSetNotification;
use Auth;
use Session;
use DB;
use Cookie;
use App\Services\ActivityLogService;
use App\Services\AuthService;
use InvalidArgumentException;

class LoginController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ckname = explode('_', Auth::getRecallerName())[2];

        if (! request()->has('template')) {
            $this->middleware('guest:user')->except('logout', 'impersonate', 'cancelImpersonate');
        }
    }

    // use AuthenticatesUsers;
    /**
     * @return login page view
     */
    public function showLoginForm(AuthService $authService)
    {
        // Now log in the user if exists
        if ($authService->needsAutoAuthentication()) {
            $authService->autoAuthenticate();

            return redirect()->intended(route('site.index'));
        }


        if (strpos(url()->current(), 'admin') !== false) {
            return redirect('/auth/login');
        }

        $value = Cookie::get($this->ckname);
        if (! is_null($value) && ! request()->has('template')) {
            $rememberedUser = explode('.', explode($this->ckname, decrypt($value))[1]);
            if ($rememberedUser[1] == 'user' && Auth::guard('user')->loginUsingId($rememberedUser[0])) {
                $ckkey = encrypt($this->ckname . Auth::user()->id . '.user');
                Cookie::queue($this->ckname, $ckkey, 2592000);

                return redirect()->intended(url('login'));
            }
        }

        $data['settings'] = $this->getAuthSettings();

        if (request()->has('template') && array_key_exists(request('template'), $data['settings']) && auth()->user() && auth()->user()->role()->type == 'admin') {
            $data['template'] = request('template');
        }

        try {
            return view('admin.auth.partial.login', $data);
        } catch (InvalidArgumentException $th) {
            return view('admin.auth.login');
        }
    }

    /**
     * Login authenticate operation.
     *
     * @return redirect dashboard page
     */
    public function authenticate(AuthUserRequest $request)
    {
        if (empty($request->email) && empty($request->phone)) {
            return back()->withInput()->withErrors(['error' => __('Please provide either email or phone number.')]);
        }
        
        if (!empty($request->email) && !empty($request->phone)) {
            return back()->withInput()->withErrors(['error' => __('Please provide only one login method.')]);
        }
        
        $loginType = $request->email ? 'email' : 'phone';
        $data = $request->only($loginType, 'password');
        $data['status'] = 'Active';
        $userData = User::where($loginType, $data[$loginType])->first();

        // Check if user exists before accessing password
        if (!$userData) {
            (new ActivityLogService())->userLogin('failed', 'Incorrect');

            return back()->withInput()->withErrors(['email' => __('Invalid email or password')]);
        }

        if (\Hash::check($data['password'], $userData->password)) {
            if ($userData->status == 'Pending' && isActive('SaaS')) {
                Session::put('martvill-seller', $userData);
                User::where('id', $userData->id)->update(['activation_code' => \Str::random(10), 'activation_otp' => random_int(1111, 9999)]);

                return back()->withInput()->withErrors(['error' => __('Please verify your email or phone.') . ' <a href="' . route('saas.registration.otp') . '" class="underline cursor-pointer text-gray-12">' . __('Click here to verify.') . '</a>']);
            }
            if ($userData->status != 'Active') {
                (new ActivityLogService())->userLogin('failed', 'Inactive');

                return back()->withInput()->withErrors(['error' => __('Inactive User')]);
            }
            if ($userData->role()->type == 'vendor' && $userData->vendors()->first()->status != 'Active') {
                (new ActivityLogService())->userLogin('failed', 'Inactive');

                return back()->withInput()->withErrors(['error' => __('Inactive Vendor, Please contact with administrator or wait for admin approval.')]);
            }

            if (Auth::guard('user')->attempt($data)) {
                session()->put('vendorId', optional(auth()->user()->vendor())->vendor_id);
                if (! is_null($request->remember)) {
                    $ckkey = encrypt($this->ckname . Auth::user()->id . '.user');
                    Cookie::queue($this->ckname, $ckkey, 2592000);
                }
                if (auth()->user()->role()->type == 'admin') {
                    if ($this->ncpc()) {
                        Session::flush();

                        return view('errors.installer-error', ['message' => __('This product is facing license validation issue.') . '<br>' . __('Please verify your purchase code from :x.', ['x' => '<a style="color:#fcca19" href="' . route('purchase-code-check', ['bypass' => 'purchase_code']) . '">' . __('here') . '</a>'])]);
                    }
                    (new ActivityLogService())->userLogin('success', 'Login successful');

                    return redirect()->intended(route('dashboard'));
                }
                if ($this->ncpc()) {
                    Session::flush();

                    return view('errors.installer-error', ['message' => __('This product is facing license validation issue.<br>Please contact admin to fix the issue.')]);
                }

                if (isActive('Pos') && version_compare(moduleData('Pos')->get('version'), '2.0', '>=')) {
                    expirePosSession(auth()->user()?->id);
                }

                if (auth()->user()->role()->type != 'vendor' || auth()->user()->vendors()->first()->status != 'Active') {
                    (new ActivityLogService())->userLogin('failed', 'Inactive');

                    return redirect()->intended(route('site.index'));
                }

                if ($userData->role()->slug === 'delivery-man') {
                    return redirect()->route('carrier.dashboard');
                }

                (new ActivityLogService())->userLogin('success', 'Login successful');

                return redirect()->intended(route('vendor-dashboard'));
            }

            return back()->withInput()->withErrors(['error' => __('Invalid User')]);
        } else {
            (new ActivityLogService())->userLogin('failed', 'Incorrect');

            return back()->withInput()->withErrors(['email' => __('Invalid email or password')]);
        }
    }

    /**
     * logout operation.
     *
     * @return redirect login page view
     */
    public function logout()
    {
        $cookie = Cookie::forget($this->ckname);
        $user = Auth::user();
        Auth::guard('user')->logout();

        if (isset($user)) {
            (new ActivityLogService())->userLogout('success', 'Logout successful', $user);
        }

        if (isActive('Affiliate')) {
            $helper = \Modules\Affiliate\Services\AffiliateHelper::getInstance();
            $helper->destroy();
        }

        if ($user && isActive('Pos') && version_compare(moduleData('Pos')->get('version'), '2.0', '>=')) {
            expirePosSessionNow($user->id);
        }

        Session::flush();

        return redirect()->route('login')->withCookie($cookie);
    }

    /**
     * forget password
     *
     * @return forget password form
     */
    public function reset()
    {
        $this->data = ['page_title' => __('Reset Password'), 'settings' => $this->getAuthSettings()];

        return view('admin.auth.passwords.email', $this->data);
    }

    /**
     * Opt form
     *
     * @return forget password form
     */
    public function resetOtp(Request $request)
    {
        $settings = $this->getAuthSettings();
        
        // Get email/phone from old input, request, or session
        $email = old('email') ?? $request->email ?? session('password_reset_email');
        $phone = old('phone') ?? $request->phone ?? session('password_reset_phone');
        $otpCreatedAt = null;
        
        // Get OTP creation timestamp from password_resets table, scoped to the provided email/phone
        // Only query if we have at least one identifier to prevent cross-user OTP leakage
        if (!empty($email) || !empty($phone)) {
            // Get OTP creation timestamp from password_resets table
            // Group the email/phone conditions correctly within the same closure
            $passwordReset = PasswordReset::where(function($query) use ($email, $phone) {
                if (!empty($email) && !empty($phone)) {
                    // If both are provided, match records where email OR phone matches
                    $query->where('email', $email)
                          ->orWhere('phone', $phone);
                } elseif (!empty($email)) {
                    // Only email provided
                    $query->where('email', $email);
                } elseif (!empty($phone)) {
                    // Only phone provided
                    $query->where('phone', $phone);
                }
            })
            ->orderBy('created_at', 'desc')
            ->first();
            
            if ($passwordReset && $passwordReset->created_at) {
                $otpCreatedAt = is_string($passwordReset->created_at) ? strtotime($passwordReset->created_at) : $passwordReset->created_at->timestamp;
            }
        }
        
        // Get OTP expiration time from preference (in minutes, default 5)
        $otpExpireIn = preference('otp_expire_in', 5);

        return view('admin.auth.passwords.otp', compact('settings', 'email', 'phone', 'otpCreatedAt', 'otpExpireIn'));
    }

    /**
     * Send reset password link
     *
     * @return null
     */
    public function sendResetLinkEmail(Request $request)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];
        $validator = PasswordReset::storeValidation($request->all());
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $request['token'] = Password::getRepository()->createNewToken();
        $request['otp'] = random_int(1111, 9999);
        $request['created_at'] = date('Y-m-d H:i:s');
        $request['phone'] = !empty($request['phone']) ? $request['dial_code'] . $request['phone'] : null;

        try {
            \DB::beginTransaction();
            (new PasswordReset())->storeOrUpdate($request->only('email', 'phone', 'token', 'otp', 'created_at'));
            User::whereEmailOrPhone($request->email, $request->phone)->first()?->notify(new ResetPasswordNotification($request));

            $data['status'] = 'success';
            if ($request->phone) {
                $data['message'] = __('Password reset OTP sent to your phone number.');
            } else {
                $data['message'] = __('Password reset link sent to your email address.');
            }
            \DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['status'] = 'fail';
            $data['message'] = $e->getMessage();
        }
        $this->setSessionValue($data);

        // Store email/phone in session for OTP page
        if (!empty($request->email)) {
            session(['password_reset_email' => $request->email]);
            session(['password_reset_phone' => null]);
        }
        
        if (!empty($request->phone)) {
            session(['password_reset_phone' => $request->phone]);
            session(['password_reset_email' => null]);
        }

        if (User::userVerification('otp')) {
            return redirect()->route('reset.otp')->withInput();
        }

        return redirect()->back();
    }

    /**
     * Resend password reset OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendResetOtp(Request $request)
    {
        $email = $request->email ?? old('email');
        $phone = $request->phone ?? old('phone');

        if (empty($email) && empty($phone)) {
            return response()->json([
                'status' => 'fail',
                'message' => __('Email or phone number is required.')
            ], 400);
        }

        $user = User::whereEmailOrPhone($email, $phone)->first();

        if (empty($user)) {
            return response()->json([
                'status' => 'fail',
                'message' => __('User not found.')
            ], 404);
        }

        try {
            \DB::beginTransaction();

            $request['token'] = Password::getRepository()->createNewToken();
            $request['otp'] = random_int(1111, 9999);
            $request['created_at'] = now();
            $request['email'] = $email;
            $request['phone'] = $phone;

            (new PasswordReset())->storeOrUpdate($request->only('email', 'phone', 'token', 'otp', 'created_at'));
            
            // Get the updated password reset entry to get the exact timestamp
            $passwordReset = PasswordReset::where(function($query) use ($email, $phone) {
                if ($email && $phone) {
                    $query->where('email', $email)->orWhere('phone', $phone);
                } elseif ($email) {
                    $query->where('email', $email);
                } elseif ($phone) {
                    $query->where('phone', $phone);
                }
            })
            ->orderBy('created_at', 'desc')
            ->first();
            
            $otpCreatedAt = null;
            if ($passwordReset && $passwordReset->created_at) {
                $otpCreatedAt = is_string($passwordReset->created_at) ? strtotime($passwordReset->created_at) : $passwordReset->created_at->timestamp;
            }
            if (!$otpCreatedAt) {
                $otpCreatedAt = now()->timestamp;
            }
            
            $user->notify(new ResetPasswordNotification($request));

            \DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('OTP has been resent successfully.'),
                'otp_created_at' => $otpCreatedAt
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => __('Failed to resend OTP. Please try again.')
            ], 500);
        }
    }

    /**
     * showResetForm method
     *
     * @param  string  $tokens
     * @return show reset password page view
     */
    public function showResetForm(Request $request, $tokens = null)
    {
        if (! empty($tokens)) {
            $tokens = $request->token;
        }

        $emailToken = $phoneToken = null;

        if ($request->phone) {
            $phoneToken = PasswordReset::where('phone', $request->phone)
                ->where(function($query) use ($request) {
                    $query->where('otp', $request->token)
                          ->orWhere('token', $request->token);
                })->first();
        }

        if ($request->email) {
            $emailToken = PasswordReset::where('email', $request->email)
                ->where(function($query) use ($request) {
                    $query->where('otp', $request->token)
                          ->orWhere('token', $request->token);
                })->first();
        }

        if (!$emailToken && !$phoneToken) {
            return redirect()->route('reset.otp')->withErrors(['email' => __('Invalid token or OTP')]);
        }

        // Check if OTP has expired using the specific fetched record to prevent cross-user collisions
        $resetRecord = $emailToken ?? $phoneToken;
        if ($resetRecord && $resetRecord->isResetExpired()) {
            return redirect()->route('reset.otp')->withErrors(['otp' => __('OTP has expired. Please request a new OTP.')]);
        }

        $data = ['token' => $tokens];
        $data['user'] = (new User())->getData($tokens);

        if (! $data['user']) {
            return redirect()->back()->withErrors(['email' => __('Invalid password token')]);
        }

        $data['settings'] = $this->getAuthSettings();

        return view('admin.auth.passwords.reset', $data);
    }

    /**
     * @return redirect login page view
     */
    public function setPassword(Request $request)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];
        if ($request->isMethod('post')) {
            // Check if OTP has expired before processing
            // Fetch the specific reset record first to prevent cross-user collisions
            if (!empty($request->token)) {
                $resetRecord = PasswordReset::where('otp', $request->token)
                    ->orWhere('token', $request->token)
                    ->first();
                
                if ($resetRecord && $resetRecord->isResetExpired()) {
                    return back()->withErrors(['otp' => __('OTP has expired. Please request a new OTP.')])->withInput();
                }
            }

            $response = $this->checkExistence($request->id, 'users', ['getData' => true]);
            if ($response['status'] === true) {
                $validator = PasswordReset::passwordValidation($request->all());
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
                $request['user_name'] =  $response['data']->name;
                $request['email'] =  $response['data']->email;
                $request['raw_password'] = $request->password;
                $request['updated_at'] = date('Y-m-d H:i:s');
                $request['password'] = \Hash::make(trim($request->password));

                $updateResult = (new PasswordReset())->updatePassword($request->only('password', 'token', 'updated_at'), $request->id);
                if ($updateResult['status'] === 'success') {
                    $user = User::find($request->id);
                    if($user) {
                        $user->notify(new UserPasswordSetNotification($request));
                    }
                    
                    $data['status'] = 'success';
                    $data['message'] = $updateResult['message'];
                } else {
                    $data['message'] = $updateResult['message'];
                }
            } else {
                $data['message'] = $response['message'];
            }
        }

        $this->setSessionValue($data);

        return redirect()->route('login');
    }

    /**
     * Impersonate as another user
     *
     * @return redirect
     */
    public function impersonate(Request $request)
    {
        $password = techDecrypt($request->impersonate);

        $user = User::where('password', $password)->first();

        if (! session()->has('impersonator')) {
            session(['impersonator' => auth()->id()]);
        }
        Auth::loginUsingId($user->id);
        session()->put('vendorId', optional(auth()->user()->vendor())->vendor_id);

        return redirect(route('site.index'));
    }

    /**
     * Cancel Impersonate
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function cancelImpersonate()
    {
        Auth::loginUsingId(session('impersonator'));
        session()->forget('impersonator');
        session()->forget('vendorId');

        return redirect(route('dashboard'));
    }

    public function ncpc()
    {
        if (! g_e_v()) {
            return true;
        }
        if (! g_c_v()) {
            try {
                $d_ = g_d();
                $e_ = g_e_v();
                $e_ = explode('.', $e_);
                $c_ = md5($d_ . $e_[1]);
                if ($e_[0] == $c_) {
                    p_c_v();

                    return false;
                }

                return true;
            } catch (\Exception $e) {
                return true;
            }
        }

        return false;
    }

    /**
     * Auth Settings
     *
     * @return array
     */
    private function getAuthSettings()
    {
        $authSettingJson = preference('auth_settings', []) ?: defaultAuthSettings();

        return json_decode($authSettingJson, true);
    }

    /**
     * showRegisterForm method
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        if (auth()->user()) {
            return redirect()->back();
        }

        return view('admin.auth.register');
    }
}
