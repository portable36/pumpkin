<?php

namespace Modules\Delivery\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\WithdrawalMethod;
use Illuminate\Http\Request;
use Modules\Delivery\Http\Requests\Api\V1\{
    BankSettingRequest,
    PaypalSettingRequest,
    WithdrawalRequest
};
use Modules\Delivery\Http\Resources\{
    WithdrawalMethodResource,
    WithdrawalPaymentMethodResource,
    WithdrawalResource
};
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
     * @return response
     */
    public function index(Request $request)
    {
        $configs = $this->initialize([], $request->all());
        $transaction = Transaction::select('transactions.id', 'currency_id', 'transactions.status', 'withdrawal_method_id', 'amount', 'charge_amount', 'commission_amount', 'discount_amount', 'total_amount', 'transaction_type', 'transaction_date', 'transactions.updated_at')->where('user_id', auth()->guard('api')->user()->id)->where('transaction_type', 'Delivery_withdrawal')->with(['currency:id,name', 'withdrawalMethod:id,method_name']);

        $transaction->orderBy('transaction_date', 'desc');

        return $this->response([
            'data' => WithdrawalResource::collection($transaction->paginate($configs['rows_per_page'])),
            'pagination' => $this->toArray($transaction->paginate($configs['rows_per_page'])->appends($request->all())),
        ]);
    }

    /**
     * Withdrawal Request
     *
     * @return response
     */
    public function withdraw(WithdrawalRequest $request)
    {
        $user = auth()->guard('api')->user();
        if ($user->withdrawalSettings()->where('withdrawal_method_id', $request['withdrawal_method_id'])->count() == 0) {
            $response['status'] = false;
            $response['message'] = __('Please update withdrawal setting.');

            return $this->unprocessableResponse($response);
        }

        $request['user_id'] = auth()->guard('api')->user()->id;
        $request['total_amount'] = $request['amount'];
        $request['transaction_type'] = 'Delivery_withdrawal';
        $request['status'] = 'Pending';
        $response = (new Transaction())->storeData($request->only('withdrawal_method_id', 'currency_id', 'amount', 'user_id', 'total_amount', 'transaction_type', 'transaction_date', 'status'), $user);

        if ($response['status'] == 'fail') {
            return $this->unprocessableResponse($response);
        }

        $transaction = Transaction::select('transactions.id', 'currency_id', 'transactions.status', 'withdrawal_method_id', 'amount', 'charge_amount', 'commission_amount', 'discount_amount', 'total_amount', 'transaction_type', 'transaction_date', 'transactions.updated_at')->where('user_id', auth()->guard('api')->user()->id)->where('transaction_type', 'Delivery_withdrawal')->with(['currency:id,name', 'withdrawalMethod:id,method_name'])->orderBy('id', 'desc')->first();

        return  $this->response([
            'data' => new WithdrawalResource($transaction),
        ]);
    }

    /**
     * Withdrawal payment method
     *
     * @return response
     */
    public function paymentMethod()
    {
        $paymentMethods = WithdrawalMethod::getAll()->where('status', 'Active');

        if (! $paymentMethods) {
            $response['status'] = false;
            $response['message'] = __('Payment Methods are not available at this moment.');

            return $this->unprocessableResponse($response);
        }

        return $this->response([
            'data' => WithdrawalMethodResource::collection($paymentMethods),
        ]);
    }

    /**
     * Withdrawal method
     *
     * @return response
     */
    public function method($methodId)
    {
        $paymentMethod = auth()->guard('api')->user()->withdrawalSettings()->where('withdrawal_method_id', $methodId)->first();

        if (! $paymentMethod) {
            return $this->response([
                'status' => false,
                'message' => __('User withdrawal settings does not exist.'),
                'data' => [],
            ]);
        }

        return $this->response([
            'data' => new WithdrawalPaymentMethodResource($paymentMethod),
        ]);
    }

    /**
     * Paypal setting
     *
     * @return response
     */
    public function paypalSetting(PaypalSettingRequest $request)
    {
        $response = $this->checkExistence($request->method_id, 'withdrawal_methods', ['getData' => true]);

        if (! $response['status']) {
            return $this->unprocessableResponse($response);
        }

        $request['method_name'] = $response['data']->method_name;
        $response = $this->handleWithdrawalSettingUpdate($request);

        return  $this->response([
            'data' => $response,
        ]);
    }

    /**
     * Bank setting
     *
     * @return response
     */
    public function bankSetting(BankSettingRequest $request)
    {
        $response = $this->checkExistence($request->method_id, 'withdrawal_methods', ['getData' => true]);

        if (! $response['status']) {
            return $this->unprocessableResponse($response);
        }

        $request['method_name'] = $response['data']->method_name;
        $response = $this->handleWithdrawalSettingUpdate($request);

        return  $this->response([
            'data' => $response,
        ]);
    }

    private function handleWithdrawalSettingUpdate(Request $request)
    {
        $this->withdrawalService->setWithdrawalParam($request);

        $data = $this->withdrawalService->prepareWithdrawalSettingData($request);

        return $this->withdrawalService->updateUserWithdrawalSetting($data);
    }
}
