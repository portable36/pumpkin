<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 *
 * @created 16-10-2025
 */

namespace App\Http\Controllers\Vendor;

use App\DataTables\Vendor\CustomerLedgerDataTable;
use App\DataTables\VendorCustomerDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\Customer\StoreCustomerRequest;
use App\Http\Requests\Vendor\Customer\UpdateCustomerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\AjaxSelectSearchResource;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderMeta;
use App\Models\Transaction;
use App\Services\Order\InvoiceService;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * vendor customer list
     *
     * @return mixed
     */
    public function index(VendorCustomerDataTable $dataTable)
    {
        if (! preference('is_vendor_customer_list_active')) {
            abort(403);
        }

        return $dataTable->render('vendor.customer.index');
    }

    /**
     * Get users by search key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function findUser(Request $request)
    {
        $users = User::whereLike('name', $request->q)->limit(10)->get();

        return AjaxSelectSearchResource::collection($users);
    }

    /**
     * Create
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $data['countries'] = \Modules\GeoLocale\Entities\Country::select('id', 'name', 'code')->orderBy('name')->get();

        return view('vendor.customer.create', $data);
    }

    /**
     * Store
     *
     * @param  Request  $request  [description]
     * @return \Illuminate\Routing\Redirector
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];
        $request['password'] = \Hash::make($request->password);
        $request['vendor_id'] = session('vendorId') ?? auth()->user()->vendor()->vendor_id;
        $request['email'] = validateEmail($request->email) ? strtolower($request->email) : null;

        try {
            DB::beginTransaction();

            $customerId = (new Customer())->storeCustomer($request->only('vendor_id', 'name', 'email', 'phone', 'password', 'status'));
            (new CustomerAddress())->storeCustomerAddress($request->except('password', 'status') + ['customer_id' => $customerId, 'is_default' => 1]);

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Customer Info')]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = $e->getMessage();
        }

        $this->setSessionValue($data);

        return redirect()->route('vendor.customer');
    }

    /**
     * Edit a customer
     *
     * @param  int  $id  The customer ID
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Customer $customer)
    {

        $address = $customer->addresses()->where('is_default', 1)->first();
        $data = (new \App\Services\CustomerService())->getCustomerAddressData($address);
        $data['customer'] = $customer;

        return view('vendor.customer.edit', $data);
    }

    /**
     * Update a customer
     *
     * @param  Request  $request  The request object
     * @param  int  $id  The customer ID
     * @return \Illuminate\Routing\Redirector
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = ['status' => 'fail', 'message' => __('Invalid Request')];

        $request['email'] = validateEmail($request->email) ? strtolower($request->email) : null;
        $request['customer_id'] = $customer->id;

        $address = $customer->addresses()->where('is_default', 1)->first();

        try {
            DB::beginTransaction();

            (new Customer())->updateCustomer($request->only('name', 'email', 'phone'), $customer);
            (new CustomerAddress())->updateCustomerAddress($request->all(), $address);

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Customer Info')]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = $e->getMessage();
        }

        $this->setSessionValue($data);

        return redirect()->route('vendor.customer');
    }

    /**
     * Delete a customer
     *
     * @param  int  $id  The customer ID
     * @return \Illuminate\Routing\Redirector
     */
    public function destroy(Customer $customer)
    {
        $data = ['status' => 'fail'];
        $vendorId = session('vendorId') ?? auth()->user()->vendor()->vendor_id;

        if ($customer->vendor_id != $vendorId) {
            $data['message'] = __('You are not authorized to delete this customer.');
            $this->setSessionValue($data);

            return back();
        }

        try {
            DB::beginTransaction();

            $customer->delete();

            CustomerAddress::where(['customer_id' => $customer->id])->delete();

            $orderIds = Order::where(['customer_id' => $customer->id])->pluck('id')->toArray();

            OrderDetail::whereIn('order_id', $orderIds)->delete();

            OrderMeta::whereIn('order_id', $orderIds)->delete();

            Order::where(['customer_id' => $customer->id])->delete();

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully deleted.', ['x' => __('Customer')]);


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = $e->getMessage();
        }

        $this->setSessionValue($data);

        return back();
    }

    /**
     * Find customer by vendor
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function findCustomerByVendor(Request $request)
    {
        $query = Customer::where('vendor_id', $request->vendorId);

        if ($request->filled('q')) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->whereLike('name', $request->q)
                    ->orWhereLike('email', $request->q)
                    ->orWhereLike('phone', $request->q);
            });
        }

        $customers = $query->latest()->limit(10)->get();

        return AjaxSelectSearchResource::collection($customers);
    }

    /**
     * Customer ledger
     *
     * @return \Illuminate\Http\Response
     */
    public function ledger(CustomerLedgerDataTable $dataTable, $customerId)
    {
        $vendorId = session('vendorId') ?: auth()->user()->vendor()->vendor_id;
        $data['from'] = 'vendor';
        $data['customer'] = Customer::where('id', $customerId)->where('vendor_id', $vendorId)->first();

        if (! $data['customer']) {
            return redirect()->back()->withFail(__('Customer not found.'));
        }

        $orders = Order::where('customer_id', $customerId)
            ->where('channel', '!=', 'web')
            ->whereHas('orderDetails', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->get();

        $data['orderTotal'] = $orders->sum('total');

        $data['transactionTotal'] = Transaction::join('orders', 'transactions.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customerId)
            ->where('transactions.vendor_id', $vendorId)
            ->whereIn('transaction_type', ['Order_actual_price', 'Order_partial_payment', 'Order_payment'])
            ->sum('transactions.paid_amount');

        $data['paymentMethods'] = (new \Modules\Gateway\Entities\GatewayModule())->payableGateways();

        return $dataTable->with(['from' => 'vendor'])->render('common.customer.ledger', $data);
    }

    /**
     * Supplier payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payment($id)
    {
        $vendorId = auth()->user()?->vendor()?->vendor_id;

        $customer = Customer::where('id', $id)->where('vendor_id', $vendorId)->first();

        if (! $customer) {
            return redirect()->back()->withFail(__('Customer not found.'));
        }

        $orders = Order::where('customer_id', $id)
            ->where('payment_status', '!=', 'Paid')
            ->orderByDesc('order_date')
            ->orderByDesc('reference')
            ->get();

        return view('common.customer.payment', [
            'customer'  => $customer,
            'from'      => 'vendor',
            'orders' => $orders,
        ]);
    }

    /**
     * Store supplier payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentStore(Request $request, $id)
    {
        $vendorId = auth()->user()?->vendor()?->vendor_id;

        $customer = Customer::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->first();

        if (! $customer) {
            return redirect()->back()->withErrors(__('Customer does not exist.'));
        }

        // Validate amount_received and orders input
        $ordersInput = $request->input('orders'); // Expecting array: ['order_id' => amount_received, ...]
        if (empty($ordersInput) || ! is_array($ordersInput)) {
            return redirect()->back()->withErrors(__('No orders selected or invalid payment data.'));
        }

        $successOrders = [];
        $failedPayments = [];

        foreach ($ordersInput as $orderId => $amountReceived) {
            // Ignore zero payments silently (do not display any error for zero)
            if ($amountReceived === null || $amountReceived === '' || $amountReceived == 0) {
                continue;
            }

            // Try to find the order early for error messaging
            $order = Order::where('id', $orderId)
                ->where('customer_id', $customer->id)
                ->where('payment_status', '!=', 'Paid')
                ->first();

            $orderReference = $order ? $order->reference : $orderId;

            if ($amountReceived < 0) {
                $failedPayments[] = __('Amount must be greater than 0 for order #:x', ['x' => $orderReference]);

                continue;
            }

            if (! $order) {
                $failedPayments[] = __('Order :x not found or already paid.', ['x' => $orderReference]);

                continue;
            }

            // Clone request and override amount_received for each order
            $multiPayRequest = $request->duplicate(
                array_merge($request->all(), ['amount_received' => $amountReceived])
            );
            $invoice = new InvoiceService($multiPayRequest);

            $response = $invoice->partialPayment($order);

            if (! empty($response['status'])) {
                $successOrders[] = $order->reference;
            } else {
                $failedPayments[] = __('Order #:x: ', ['x' => $order->reference ?? $orderId]) . $response['message'];
            }
        }

        $route = redirect()->route('vendor.customer.ledger', $customer->id);

        if (! empty($successOrders)) {
            $orderList = implode(', ', $successOrders);
            $message = __('Payment has been successfully processed for the following order(s): :x.', ['x' => $orderList]);
            $route = $route->with('success', $message);
        }
        if (! empty($failedPayments)) {
            $route = $route->withErrors(implode(' ', $failedPayments));
        }

        return $route;
    }
}
