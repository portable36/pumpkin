<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 *
 * @created 16-10-2025
 */

namespace App\Http\Controllers;

use App\DataTables\AdminCustomerDataTable;
use App\DataTables\Vendor\CustomerLedgerDataTable;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderMeta;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Services\Order\InvoiceService;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * customer list
     *
     * @return mixed
     */
    public function index(AdminCustomerDataTable $dataTable)
    {
        $data['vendors'] = Vendor::where('status', 'Active')->get();

        return $dataTable->render('admin.customer.index', $data);
    }

    /**
     * Create
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $data['vendors'] = Vendor::where('status', 'Active')->get();
        $data['countries'] = \Modules\GeoLocale\Entities\Country::select('id', 'name', 'code')->orderBy('name')->get();

        return view('admin.customer.create', $data);
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

        return redirect()->route('customers.index');
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
        $data['vendors'] = Vendor::get();
        $data['customer'] = $customer;
        $data['from'] = 'admin';

        return view('admin.customer.edit', $data);
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
        $request['customer_id'] = $customer->id;
        $request['email'] = validateEmail($request->email) ? strtolower($request->email) : null;

        $address = $customer->addresses()->where('is_default', 1)->first();

        try {
            DB::beginTransaction();

            (new Customer())->updateCustomer($request->only('name', 'email', 'phone', 'vendor_id'), $customer);
            (new CustomerAddress())->updateCustomerAddress($request->all(), $address);

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Customer Info')]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = $e->getMessage();
        }

        $this->setSessionValue($data);

        return redirect()->route('customers.index');
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
     * Customer ledger
     *
     * @return \Illuminate\Http\Response
     */
    public function ledger(CustomerLedgerDataTable $dataTable, $customerId)
    {
        $data['from'] = 'admin';
        $data['customer'] = Customer::where('id', $customerId)->first();

        if (! $data['customer']) {
            return redirect()->back()->withFail(__('Customer not found.'));
        }

        $orders = Order::where('customer_id', $customerId)
            ->where('channel', '!=', 'web')
            ->get();

        $data['orderTotal'] = $orders->sum('total');

        $data['transactionTotal'] = Transaction::join('orders', 'transactions.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customerId)
            ->whereIn('transaction_type', ['Order_actual_price', 'Order_partial_payment', 'Order_payment'])
            ->sum('transactions.paid_amount');

        $data['paymentMethods'] = (new \Modules\Gateway\Entities\GatewayModule())->payableGateways();

        return $dataTable->with('from', 'admin')->render('common.customer.ledger', $data);
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
            'from'      => 'admin',
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

        $route = redirect()->route('customer.ledger', $customer->id);

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
