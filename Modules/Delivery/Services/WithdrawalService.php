<?php

namespace Modules\Delivery\Services;

use App\Models\UserWithdrawalSetting;
use Illuminate\Http\Request;

class WithdrawalService
{
    /**
     * Set withdrawal param
     *
     * @return void
     */
    public function setWithdrawalParam(Request $request)
    {
        if ($request->method_name === 'Paypal') {
            $request['param'] = json_encode(['email' => $request->email]);
        } elseif ($request->method_name === 'Bank') {
            $request['param'] = json_encode($request->except(['_token', 'method_id', 'is_default']));
        }
    }

    /**
     * Prepare Withdrawal setting data
     *
     * @return array
     */
    public function prepareWithdrawalSettingData(Request $request)
    {
        return [
            'user_id' => auth()->user()->id,
            'withdrawal_method_id' => $request->method_id,
            'param' => $request->param ?? null,
            'is_default' => $request->is_default,
        ];
    }

    /**
     * Update user withdrawal setting
     *
     * @param  array|object  $data
     * @return mixed
     */
    public function updateUserWithdrawalSetting($data)
    {
        return (new UserWithdrawalSetting())->updateData(
            array_intersect_key($data, array_flip(['user_id', 'withdrawal_method_id', 'param', 'is_default']))
        );
    }
}
