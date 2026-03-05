<?php

namespace Modules\Delivery\Http\Controllers\Carrier;

use App\DataTables\UserActivityDataTable;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Delivery\Http\Requests\Carrier\PasswordUpdateRequest;
use App\Models\{
    Role,
    User
};
use Illuminate\Support\Facades\{
    Auth,
    Cookie,
    Hash,
    Session
};
use Modules\Delivery\DataTables\EarningDataTable;
use Modules\Delivery\Http\Requests\Carrier\ProfileUpdateRequest;
use Modules\Delivery\Services\Mail\UpdateCarrierPasswordMailService;

class ProfileController extends Controller
{
    public $ckName;

    public function __construct()
    {
        $this->ckName = explode('_', Auth::getRecallerName())[2];
    }

    /**
     * Display a listing of the resource.
     */
    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $response = ['status' => 'fail', 'message' => __('Invalid Request')];
            $result = $this->checkExistence(Auth::user()->id, 'users');

            if (! $result['status']) {
                throw new \Exception($result['message']);
            }

            if ((new user())->updateUser($request->only('name', 'email', 'phone', 'birthday', 'address', 'gender'), Auth::user()->id)) {
                $response = ['status' => 'success', 'message' => __('Your information has been successfully saved.')];
            }

        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        $this->setSessionValue($response);

        return to_route('carrier.profile');
    }

    /**
     * Show the specified resource.
     */
    public function profile(): View
    {
        $userId = Auth::guard('user')->id();
        $data['user'] = User::with('deliveryMan')->find($userId);
        $data['roleIds'] = $data['user']->roles->pluck('id')->toArray();
        $data['roles'] = Role::getAll();
        $data['deliveryMan'] = $data['user']->deliveryMan->first();

        return view('delivery::carrier.profile.index', $data);
    }

    /**
     * delivery man earning history
     */
    public function earning(EarningDataTable $dataTable)
    {
        return $dataTable->render('delivery::carrier.earning');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updatePassword(PasswordUpdateRequest $request, UpdateCarrierPasswordMailService $mailService): RedirectResponse
    {
        $user = Auth::user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            $mailService->send($user);

            return redirect()->route('carrier.dashboard')->with('success', 'Password updated successfully.');
        }

        return back()->withErrors(['old_password' => 'The provided old password is incorrect.'])->withInput();
    }

    /**
     * logout operation.
     *
     * @return redirect login page view
     */
    public function logout(): RedirectResponse
    {
        $cookie = Cookie::forget($this->ckName);
        $user = Auth::user();
        Auth::guard('user')->logout();

        if (isset($user)) {
            (new ActivityLogService())->userLogout('success', 'Logout successful', $user);
        }

        Session::flush();

        return redirect()->route('login')->withCookie($cookie);
    }

    /**
     * Show only vendor activity
     */
    public function activity(UserActivityDataTable $dataTable): mixed
    {
        return $dataTable->render('delivery::carrier.profile.activity', ['logTypes' => ['USER LOGIN', 'USER LOGOUT']]);
    }
}
