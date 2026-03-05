<?php

namespace Modules\Delivery\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{
    User,
    Preference
};
use App\Services\Mail\UserSetPasswordMailService;
use Modules\Delivery\Http\Requests\Admin\PasswordUpdateRequest;
use Illuminate\Http\{
    RedirectResponse,
    Request,
};
use Illuminate\Support\Facades\{
    Hash,
    Session
};
use Illuminate\View\View;

class DeliveryController extends Controller
{
    /**
     * Delivery Dashboard
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('delivery::dashboard');
    }

    /**
     * View delivery module settings
     */
    public function settings(): View
    {
        $data['setting'] = Preference::whereIn('category', ['delivery_setting'])->pluck('value', 'field')->toArray();

        return view('delivery::admin.settings', $data);
    }

    /**
     * Store delivery man setting data
     *
     * @return $response;
     */
    public function settingStore(Request $request): RedirectResponse
    {
        $response = $this->storeData($request->except('_token'), 'delivery_setting');
        $this->setSessionValue($response);

        return to_route('admin.delivery.settings');
    }

    /**
     * Store setting data
     *
     * @param  array  $request
     * @param  string  $category
     * @return $response;
     */
    private function storeData($request, $category): mixed
    {
        $response = $this->messageArray(__('Invalid Request'), 'fail');

        $i = 0;
        foreach ($request as $key => $value) {
            $data[$i]['category'] = $category;
            $data[$i]['field']    = $key;
            $data[$i]['value'] = $value;
            $i++;
        }

        foreach ($data as $key => $value) {
            if ((new Preference())->storeOrUpdate($value)) {
                $response = $this->messageArray(__('The :x has been successfully saved.', ['x' => __('Delivery man setting')]), 'success');
            }
        }

        return $response;
    }

    /**
     * Update password
     */
    public function updatePassword(PasswordUpdateRequest $request, $id): RedirectResponse
    {
        try {
            $response = User::find($id);

            if (! $response) {
                throw new \Exception(__('User is not found.'));
            }

            $request['user_name'] =  $response->name;
            $request['email'] =  $response->email;
            $request['raw_password'] = $request->password;
            $request['updated_at'] = date('Y-m-d H:i:s');
            $request['password'] = Hash::make(trim($request->password));

            if (! (new User())->updateUser($request->only('password', 'updated_at'), $id)) {
                throw new \Exception(__('Nothing is updated.'));
            }

            if (isset($request->send_mail) && ! empty($request->send_mail)) {
                $a = (new UserSetPasswordMailService())->send($request);
            }

            $data['status'] = 'success';
            $data['message'] = __('Password update successfully.');

        } catch (\Exception $e) {
            $data['status'] = 'fail';
            $data['message'] = $e->getMessage();
        }

        Session::flash($data['status'], $data['message']);

        return back();
    }
}
