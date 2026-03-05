<?php

namespace Modules\Delivery\Http\Controllers\Carrier;

use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\{
    Role,
    RoleUser,
    User
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Hash,
    Session
};
use Modules\Delivery\Entities\DeliveryMan;
use Modules\Delivery\Http\Requests\Carrier\StoreCarrierRequest;
use Modules\Delivery\Services\Mail\BeACarrierMailService;
use Modules\Delivery\Services\Mail\RegisterCarrierMailService;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function showSignUpForm(): View
    {
        if (preference('delivery_signup') != '1') {
            abort(404);
        }

        return view('delivery::carrier.auth.sign_up');
    }

    /**
     * Carrier sign up data store
     */
    public function signUp(StoreCarrierRequest $request): RedirectResponse
    {
        if (preference('delivery_signup') != '1') {
            abort(404);
        }

        $response = $this->messageArray(__('Invalid Request'), 'fail');
        $request['password'] = Hash::make($request->password);
        $request['status'] = preference('delivery_default_signup_status') ?? 'Pending';
        $user = User::whereEmail($request->email)->first();

        if ($user) {
            $response['status'] = 'info';
            $response['message'] = __('The email address has already been taken.');
            $this->setSessionValue($response);

            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            // Store user information
            if (empty($user)) {
                $user_id = (new User())->store($request->only('name', 'email', 'gender', 'address', 'password', 'activation_code', 'activation_otp', 'status'));
            } else {
                $user_id = $user->id;
            }

            // Store delivery information
            $carrierData['metaData'] = [];
            $carrierData['essentialData'] = [
                'user_id'           => $user_id,
                'unique_id'         => DeliveryMan::getUniqueCarrierId(),
                'license_status'    => $request->license_status,
                'is_active'         => $request->is_active,
            ];

            if (request()->has('file_id') && is_array(request()->file_id)) {
                $carrierData['metaData']['driving_license_photo'] = $request->file_id['0'];
            }

            (new DeliveryMan())->store($carrierData);

            if (empty($user_id)) {
                throw new \Exception(__('Error Processing Request'));
            }

            $roleId = Role::where('slug', 'delivery-man')->first()->id;
            $roles = ['user_id' => $user_id, 'role_id' =>  $roleId];

            if (! empty($roles)) {
                (new RoleUser())->update($roles);
            }

            (new RegisterCarrierMailService())->send($request);

            DB::commit();
            $response = $this->messageArray(__('The :x has been successfully saved.', ['x' => __('delivery')]), 'success');
        } catch (\Exception $e) {
            DB::rollBack();

            $response['status'] = 'fail';
            $response['message'] = __('Failed! Something has gone wrong. Please contact with admin.');
            $this->setSessionValue($response);

            return redirect()->back();
        }

        $prefer = preference();
        if ($prefer['email'] == 'token') {
            $response['message'] = __('Success! Registration has been done and account activation key has been sent your account.');
            $this->setSessionValue($response);

            return redirect()->route('login');
        }
        Session::put('martvill-carrier', User::find($user_id));

        return redirect()->route('carrier.otp');
    }

    /**
     * Opt form
     */
    public function otpForm(): mixed
    {
        $user = Session::get('martvill-carrier');
        if ($user) {
            $data['user'] = User::find($user->id);
        }
        if (isset($data['user']) && ! empty($data['user']) && empty($data['user']->email_verified_at)) {
            return view('delivery::carrier.auth.otp', $data);
        }

        return redirect()->route('site.login');
    }

    /**
     * showResetForm method
     */
    public function otpVerification(Request $request): RedirectResponse
    {
        if (empty($request->token)) {
            return redirect()->back()->withErrors(['otp' => __('The OTP field is required.')]);
        }

        $user = User::where('activation_otp', $request->token)->whereEmail($request->email)->first();
        if (empty($user)) {
            $response['message'] = __('Your OTP is invalid.');

            return redirect()->back()->withErrors(['otp' => __('Your OTP is invalid.')]);
        }

        $user->update(['activation_otp' => null, 'activation_code' => null, 'status' => 'Active', 'email_verified_at' => now()]);
        Session::forget('martvill-seller');

        return redirect()->route('login');
    }

    /**
     * Carrier Verification
     */
    public function verification($code): mixed
    {
        $user = User::where('activation_code', $code)->first();
        if (empty($user)) {
            $msg = __('Invalid Request');

            return $this->login($msg);
        } elseif ($user->status == 'Active') {
            $msg = __('This account is already activated.');

            return $this->login($msg);
        }

        (new User())->updateUser(['status' => 'Active', 'activation_code' => null, 'activation_otp' => null, 'email_verified_at' => now()], $user->id);
        $msg = __('Your account is activated, please login.');
        Session::forget('martvill-carrier');

        return $this->login($msg);
    }

    /**
     * @return login page view
     */
    public function login($verifyMsg = null)
    {
        if (session()->get('prev1') == session()->get('prev3')) {
            return redirect('/admin')->with('loginRequired', true);
        }
        if (isset(Auth::user()->id)) {
            return back();
        }
        if (! is_null($verifyMsg)) {
            return redirect('/admin')->with('loginRequired', true)->with('verifyMsg', $verifyMsg);
        }

        return back()->with('loginRequired', true);
    }

    /**
     * Re-send carrier verification code
     */
    public function resendVerificationCode(Request $request): bool
    {
        $data['activation_code'] = Str::random(10);
        $data['activation_otp'] = random_int(1111, 9999);

        $user = User::whereEmailOrPhone($request->email, $request->phone)->first();

        $result = (new User())->updateUser($data, $user->id);
        $result = User::find($user->id);
        (new BeACarrierMailService())->send($request);

        return true;
    }
}
