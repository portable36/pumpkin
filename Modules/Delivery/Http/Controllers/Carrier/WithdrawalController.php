<?php

namespace Modules\Delivery\Http\Controllers\Carrier;

use App\Models\WithdrawalMethod;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Modules\Delivery\DataTables\WithdrawalHistoryDataTable;
use Modules\Delivery\Services\WithdrawalService;

class WithdrawalController extends Controller
{
    /**
     * Withdrawal service
     */
    public $withdrawalService;

    /**
     * Constructor for withdrawal controller.
     */
    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    /**
     * Withdrawal list
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(WithdrawalHistoryDataTable $dataTable)
    {
        return $dataTable->render('delivery::carrier.withdrawal.index');
    }

    /**
     * Withdrawal setting
     *
     * @return mixed
     */
    public function setting(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->showWithdrawalSettingPage();
        } elseif ($request->isMethod('post')) {
            return $this->handleWithdrawalSettingUpdate($request);
        }
    }

    /**
     * Withdraw
     *
     * @return mixed
     */
    public function withdraw(Request $request)
    {
        if ($request->isMethod('get')) {
            $data['methods'] = WithdrawalMethod::getAll()->where('status', 'Active');

            return view('delivery::carrier.withdrawal.withdraw', $data);
        } elseif ($request->isMethod('post')) {
            $validator = Transaction::withdrawValidation($request->all());
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if (auth()->user()->withdrawalSettings()->where('withdrawal_method_id', $request['withdrawal_method_id'])->count() == 0) {
                return redirect()->route('carrier.withdrawal_setting')->withFail(__('Please update withdrawal setting.'));
            }

            $request['total_amount'] = $request['amount'];
            $request['transaction_type'] = 'Delivery_withdrawal';
            $request['status'] = 'Pending';
            $response = (new Transaction())->storeData($request->only('withdrawal_method_id', 'currency_id', 'amount', 'user_id', 'total_amount', 'transaction_type', 'transaction_date', 'status'));

            $this->setSessionValue($response);

            return $response['status'] == 'fail' ? back() : redirect()->route('carrier.withdrawal');
        }
    }

    /**
     * Show withdrawal setting page
     *
     * @return view
     */
    private function showWithdrawalSettingPage()
    {
        $activeMethods = WithdrawalMethod::getAll()->where('status', 'Active');

        return view('delivery::carrier.withdrawal.setting', ['methods' => $activeMethods]);
    }

    /**
     * Handle withdrawal setting update
     *
     * @return redirect
     */
    private function handleWithdrawalSettingUpdate(Request $request)
    {
        $response = $this->validateWithdrawalMethod($request->method_id);

        if (! $response['status']) {
            return redirect()->back()->with('error', $response['message']);
        }

        $request['method_name'] = $response['data']->method_name;

        $this->withdrawalService->setWithdrawalParam($request);

        $data = $this->withdrawalService->prepareWithdrawalSettingData($request);
        $response = $this->withdrawalService->updateUserWithdrawalSetting($data);

        $this->setSessionValue($response);

        return back();
    }

    /**
     * Check withdrawal method
     *
     * @param  string|int  $id
     * @return mixed
     */
    private function validateWithdrawalMethod($id)
    {
        return $this->checkExistence($id, 'withdrawal_methods', ['getData' => true]);
    }
}
