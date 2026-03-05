<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 *
 * @created 19-01-2022
 */

namespace App\Http\Controllers\Vendor;

use App\DataTables\VendorOrderDataTable;
use App\Exports\VendorOrderListExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\StoreOrderShippingTrackRequest;
use App\Services\Order\InvoiceService;
use Illuminate\Http\Request;
use App\Services\Actions\OrderAction;
use Modules\Refund\Entities\RefundReason;
use App\Models\{Address,
    Customer,
    CustomerAddress,
    Order,
    OrderDetail,
    OrderStatus,
    OrderStatusHistory,
    OrderStatusRole,
    OrderNoteHistory,
    OrderShippingTrack,
    Preference,
    Transaction,
    User,
    Vendor};
use App\Notifications\OrderInvoiceNotification;
use Excel;
use DB;
use Auth;
use Illuminate\Support\Arr;
use Modules\GeoLocale\Entities\Country;
use Modules\GeoLocale\Entities\Division;
use Modules\Inventory\Entities\Location;
use Modules\Tax\Entities\TaxClass;

class VendorOrderController extends Controller
{
    /**
     * vendor order list
     *
     * @return mixed
     */
    public function index(vendorOrderDataTable $dataTable)
    {
        $data['statuses'] = OrderStatus::getAll()->sortBy('order_by');

        return $dataTable->render('vendor.orders.index', $data);
    }

    /**
     * Edit order
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Request $request, $id)
    {
        $data['order'] = Order::where('id', $id)->first();

        if (empty($data['order'])) {
            return redirect()->route('vendorOrder.index')->withErrors(__('Order not found'));
        }

        $data['vendor'] = Vendor::find($data['order']->orderDetails[0]?->vendor_id);
        $data['location'] = Location::find($data['order']->location_id);

        $request->merge([
            'custom_location' => true,
            'location_id' => $data['order']->location_id,
        ]);

        $billingAddress = $data['order']->getBillingAddress();
        $country = Country::where('code', $billingAddress->country)->first();
        $billingAddress->country_name = $country?->name;
        $billingAddress->state_name = Division::where(['country_id' => $country?->id, 'code' => $billingAddress->state])->value('name');
        $data['billingAddress'] = $billingAddress;

        $shippingAddress = $data['order']->getShippingAddress();
        $country = Country::where('code', $shippingAddress->country)->first();
        $shippingAddress->country_name =  $country?->name;
        $shippingAddress->state_name = Division::where(['country_id' => $country?->id, 'code' => $shippingAddress->state])->value('name');
        $data['shippingAddress'] = $shippingAddress;

        $data['orderStatus'] = OrderStatus::getAll()->sortBy('order_by');
        $data['paymentMethods'] = (new \Modules\Gateway\Entities\GatewayModule())->payableGateways();
        $data['taxes'] = TaxClass::getAll()->toArray();
        $data['panel'] = 'vendor';
        $data['unit'] = defaultUnit();

        $userId   = $data['order']->user?->id ?? $data['order']->customer?->id;
        $vendorId =  $data['vendor']->id;
        $data['allAddresses'] = [];

        // Fetch all addresses in one query
        $addresses = CustomerAddress::where('customer_id', $userId)
            ->where('vendor_id', $vendorId)
            ->get();

        if ($addresses->isNotEmpty()) {
            $data['allAddresses'] = $addresses->map(function ($address) {
                $addressData = Arr::only($address->toArray(), [
                    'id', 'customer_id', 'company_name',
                    'type_of_place', 'address_1', 'address_2',
                    'city', 'state', 'zip', 'country',
                    'is_default', 'created_at', 'updated_at',
                ]);

                // Safely fetch related names with fallback values
                $addressData['country_name'] = Country::where('code', $address->country)->value('name') ?? __('Unknown Country');
                $addressData['state_name']   = Division::getStateNameByCountryStateCode($address->country, $address->state) ?? __('Unknown State');

                return $addressData;
            }); // Convert collection to plain array
        }

        return view('vendor.invoice.edit', $data);
    }

    /**
     * vendor order view
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function view(Request $request, $id = null)
    {
        if (isset($request->action) && $request->action == 'new') {
            do_action('before_vendor_create_order');
            $data['orderStatus'] = OrderStatus::getAll()->sortBy('order_by');
            $data['paymentMethods'] = (new \Modules\Gateway\Entities\GatewayModule())->payableGateways();
            $data['taxes'] = TaxClass::getAll()->toArray();
            $data['vendor'] = auth()->user()->vendor();
            $data['unit'] = defaultUnit();
            $data['location'] = Location::where(['vendor_id' => auth()->user()->vendor()->vendor_id, 'status' => 'Active'])->select('id', 'name')->orderByDesc('is_default')->first();

            return view('vendor.invoice.create', $data);
        }

        $vendorId = session()->get('vendorId') ?: auth()->user()->vendor()->vendor_id;
        $order = Order::where('id', $id)->where('id', $id)->whereHas('orderDetails', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->with('orderDetails')->first();

        if (empty($order)) {
            $order = Order::where('id', $id)->where('id', $id)->where('note', auth()->user()->id)->first();
        }

        if (! empty($order)) {
            if (in_array($order->channel, ['invoice', 'pos'])) {
                $data['order'] = $order;
                $billingAddress = $order->getBillingAddress();
                $country = Country::where('code', $billingAddress->country)->first();
                $billingAddress->country = $country?->name;
                $billingAddress->state = Division::where(['country_id' => $country?->id, 'code' => $billingAddress->state])->value('name');
                $data['billingAddress'] = $billingAddress;

                $shippingAddress = $order->getShippingAddress();
                $country = Country::where('code', $shippingAddress->country)->first();
                $shippingAddress->country = $country?->name;
                $shippingAddress->state = Division::where(['country_id' => $country?->id, 'code' => $shippingAddress->state])->value('name');
                $data['shippingAddress'] = $shippingAddress;

                $data['location'] = Location::find($order->location_id);
                $data['orderAction'] = new OrderAction();

                $data['orderStatus'] = OrderStatus::find($order->order_status_id);

                $data['paymentMethods'] = (new \Modules\Gateway\Entities\GatewayModule())->payableGateways();
                $data['transactions'] = Transaction::where('order_id', $order->id)->orderBy('transaction_date', 'DESC')->orderBy('id', 'DESC')->get()->map(function ($transaction) {
                    return [
                        'transaction_id' => json_decode($transaction->params, true)['transaction_id'] ?? null,
                        'payment_method' => json_decode($transaction->params, true)['payment_method'] ?? null,
                        'amount' => $transaction->paid_amount,
                        'date' => $transaction->transaction_date,
                    ];
                });
                $data['panel'] = 'vendor';
                $data['unit'] = defaultUnit();

                return view('vendor.invoice.view', $data);
            }

            $data['order'] = $order;
            $data['vendorId'] = $vendorId;
            $data['refundReasons'] = RefundReason::where('status', 'Active')->get();
            $data['finalOrderStatus'] = Order::getFinalOrderStatus();
            $data['orderStatus'] = OrderStatus::whereHas('orderStatusRole', function ($q) {
                $q->where('role_id', 2);
            })->orderBy('order_by', 'ASC')->get();
            $data['orderStatusHistories'] = OrderStatusHistory::join('products', 'products.id', 'order_status_histories.product_id')
                ->select('order_status_histories.*')
                ->where(['order_id' => $id, 'vendor_id' => $vendorId])
                ->orderByDesc('id')
                ->get();
            $data['orderNotes'] = OrderNoteHistory::where(['order_id' => $id, 'user_id' => auth()->user()->id])->orderBy('id', 'desc')->get();
            $data['orderAction'] = new OrderAction();
            $data['customFee'] = $order->metadata->where('key', 'meta_custom_fee')->first();
            $data['customTax'] = $order->metadata->where('key', 'meta_custom_tax')->first();
            $data['customFieldValues'] = $data['orderAction']->displayCustomFieldValues($id);
            $data['inventoryLocation'] = Location::where(['status' => 'Active', 'vendor_id' => $vendorId])->get();

            return view('vendor.orders.view', $data);
        }

        return redirect()->back();
    }

    /**
     * change status
     *
     * @return array
     */
    public function changeStatus(Request $request)
    {
        $data['status'] = 0;

        if ($request->data['type'] == 'shipmentTracking') {

            $data = $request->data;

            $validator = StoreOrderShippingTrackRequest::validateData($data);

            if ($validator->fails()) {
                $response['status'] = 0;
                $response['error'] = $validator->errors()->first();

                return $response;
            }

            if ($response = (new OrderShippingTrack())->storeData($data)) {
                return ['status' => 1, 'message' => __('The :x has been successfully saved.', ['x' => __('Shipment Tracking')])];
            }
        }
        $order = Order::where('id', $request->data['id'] ?? null)->first();

        if (isset($request->data['type']) && $request->data['type'] == 'download') {
            $order = Order::where('id', $request->data['order_id'])->first();
            if (! empty($order)) {
                return $order->revokeAccess($request);
            }
        }

        $downLoadData = json_decode($request->data['download_data']);
        $downloadArray = [];

        if (empty($order)) {
            return ['status' => 0, 'message' => __('Something went wrong, please try again.')];
        }

        if (is_array($downLoadData) && count($downLoadData) > 0) {
            $order->downloadDataMerge($downLoadData);

            if (empty($request->data['status_ids'])) {
                return ['status' => 1, 'message' => __('The :x has been successfully saved.', ['x' => __('Order')])];
            }
        }

        if (preference('order_address_edit') == 1) {
            parse_str($request->data['billing_data'], $billingData);
            parse_str($request->data['shipping_data'], $shippingData);

            if (is_array($billingData) && count($billingData) > 0) {
                $order->updateBillingData($billingData);
            }

            if (is_array($shippingData) && count($shippingData) > 0) {
                $order->updateShippingData($shippingData);
            }
        }

        try {

            DB::beginTransaction();

            $data['status'] = $this->statusUpdate($request->data['status_ids'] ?? null);

            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Order')]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] = __('Something went wrong, please try again.');
        }

        return $data;
    }

    /**
     * status update
     *
     * @return int|void
     */
    public function statusUpdate($statusIds)
    {
        if (isset($statusIds)) {
            $finalOrderStatus = Order::getFinalOrderStatus();
            foreach ($statusIds as $detailId => $statusId) {
                $orderDetail = OrderDetail::where('id', $detailId)->first();
                if (empty($orderDetail) || $orderDetail->is_delivery == 1) {
                    continue;
                }
                if ($statusId != $finalOrderStatus && $orderDetail->order_status_id != $statusId || ($statusId == $finalOrderStatus  && strtolower(optional($orderDetail->order)->payment_status) == 'paid' && $orderDetail->order_status_id != $statusId)) {

                    if ($statusId == $finalOrderStatus) {
                        (new OrderDetail())->updateOrder(['order_status_id' => $statusId, 'is_delivery' => 1, 'is_on_time' => $orderDetail->isInTime()], $orderDetail->id);
                    } else {

                        (new OrderDetail())->updateOrder(['order_status_id' => $statusId], $orderDetail->id);
                    }

                    $history['user_id'] = Auth::user()->id;
                    $history['order_id'] = $orderDetail->order_id;
                    $history['product_id'] = $orderDetail->product_id;
                    $history['order_status_id'] = $statusId;
                    (new OrderStatusHistory())->store($history);
                    $checkAllStatus = OrderDetail::where('order_id', $orderDetail->order_id)->whereHas('orderStatus', function ($q) {$q->where('slug', '!=', 'cancelled'); })->pluck('order_status_id')->toArray();
                    $checkAllStatus = array_unique($checkAllStatus);

                    if (count($checkAllStatus) == 1) {
                        if (isset($checkAllStatus[0])) {
                            $order = Order::where('id', $orderDetail->order_id)->first();
                            if ($order->order_status_id != $checkAllStatus[0]) {
                                (new Order())->updateOrder(['order_status_id' => $checkAllStatus[0]], $orderDetail->order_id);

                                if (isActive('Affiliate')) {
                                    $orderStatusInfo = OrderStatus::getAll()->where('id', $checkAllStatus[0])->first();
                                    \Modules\Affiliate\Entities\ReferralPurchase::referralPurchaseUpdate($order->id, $orderStatusInfo);
                                }

                                $history = [];
                                $history['order_id'] = $orderDetail->order_id;
                                $history['note'] = 'System Generated';
                                $history['order_status_id'] = $statusId;
                                (new OrderStatusHistory())->store($history);
                            }
                        }
                    }

                    // commission
                    if (isActive('Commission')) {
                        (new order())->orderCommission($orderDetail->id, $statusId);
                    }

                }
            }

            return 1;
        }
    }

    /**
     * check vendor order status
     *
     * @return bool
     */
    public function isOrderStatusEnable($statusId)
    {
        $orderStatus = OrderStatusRole::getAll()->where('role_id', 2)->pluck('order_status_id')->toArray();
        if (! empty($orderStatus)) {
            return in_array($statusId, $orderStatus);
        }

        return false;
    }

    /**
     * order list pdf
     *
     * @return html static page
     */
    public function pdf()
    {
        $vendorId = session()->get('vendorId');
        $data['orders'] = Order::whereHas('orderDetails', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->with('orderDetails')->get();

        return printPDF(
            $data,
            'order_lists' . time() . '.pdf',
            'vendor.orders.pdf',
            view('vendor.orders.pdf', $data),
            'pdf'
        );
    }

    /**
     * order list csv
     *
     * @return html static page
     */
    public function csv()
    {
        return Excel::download(new VendorOrderListExport(), 'order_lists' . time() . '.csv');
    }

    /**
     * order invoice print
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|void
     */
    public function invoicePrint($id)
    {
        $vendorId = session()->get('vendorId') ?: auth()->user()->vendor()->vendor_id;
        $order = Order::where('id', $id)->where('id', $id)->whereHas('orderDetails', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId);
        })->with('orderDetails')->first();
        $data['vendor'] = Vendor::where('id', $vendorId)->first();
        $data['unit'] = defaultUnit();

        if (! empty($order)) {
            $data['order'] = $order;
            $data['vendorId'] = $vendorId;
            $data['invoiceSetting'] = json_decode(preference('invoice'));
            $data['logo'] = Preference::where('field', 'company_logo')->first()->fileUrl();
            if ($data['invoiceSetting']?->document?->header?->logo == 'logo' && $data['invoiceSetting']?->general->logo) {
                $data['logo'] = Preference::where('field', 'invoice')->first()->fileUrl();
            }
            $data['orderAction'] = new OrderAction();
            $data['user'] = $order->user ?? $order->customer;
            $data['orderStatus'] = OrderStatus::getAll()->sortBy('order_by');
            $data['type'] = request()->get('type') == 'print' || request()->get('type') == 'pdf' ? request()->get('type') : 'print';
            $data['customTax'] = $order->updatedCustomTaxFee($order, true, true);
            $data['customFee'] = $order->customFeeCalculations();
            if ($data['type'] == 'pdf' || $data['type'] == 'print') {
                return printPDF($data, $order->reference . '.pdf', 'vendor.orders.invoice_print', view('vendor.orders.invoice_print', $data), $data['type']);
            } else {
                return view('vendor.orders.invoice_print', $data);
            }
        }

        return redirect()->route('vendorOrder.index');
    }

    /**
     * Store note
     *
     * @return json $response
     */
    public function storeNote(Request $request)
    {
        $user['user_id'] = auth()->user()->id;
        $data = array_merge($request->data, $user);

        $validator = OrderNoteHistory::storeValidation($data);
        if ($validator->fails()) {
            $response['status'] = 0;
            $response['error'] = $validator->errors()->first();

            return $response;
        }
        if ($response = (new OrderNoteHistory())->storeData($data)) {
            $date = timeZoneFormatDate($response->created_at) . ' ' . timeZoneGetTime($response->created_at);

            return ['status' => 1, 'date' => $date, 'note' => $response->note, 'message' => __('The :x has been successfully saved.', ['x' => __('Note')])];
        }

        return ['status' => 0, 'message' => __('Something went wrong.')];
    }

    /**
     * Order Action
     *
     * @return json $response
     */
    public function orderAction(Request $request)
    {
        if ($request->data['action_val'] == 1) {
            $order = Order::find($request->data['order_id']);
            $order['only_user'] = 'true';
            User::find($order->user_id)->notify(new OrderInvoiceNotification($order));

            return ['status' => 1, 'message' => __('Notification successfully send.')];
        }

        return ['status' => 0, 'message' => __('Something went wrong.')];
    }

    /**
     * grant access
     *
     * @return int[]
     */
    public function grantAccess(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::where('id', $orderId)->first();

        if (! empty($order)) {
            return $order->grantAccess($request);
        }

        return ['status' => 0];
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function customize(Request $request)
    {
        $order = Order::where('id', $request->order_id)->first();

        $vendorIds = $order->orderDetails->pluck('vendor_id')->toArray();

        if (! in_array(auth()->user()->vendor()->vendor_id, $vendorIds) && $order->note != auth()->user()->id || preference('order_product_edit') == 0) {
            return response()->json([
                'status' => false,
                'message' => __('Invalid Order!'),
            ]);
        }

        $invoice = new InvoiceService($request, 'vendor');

        return $invoice->customize();
    }

    /**
     * User Address
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userAddress(Request $request)
    {
        $userId   = $request->input('user_id');
        $vendorId = $request->input('vendor_id');

        $data = [
            'primary_address' => null,
            'all_addresses'   => [],
        ];

        // Fetch user once
        $user = Customer::find($userId);

        // Fetch all addresses in one query
        $addresses = CustomerAddress::where('customer_id', $userId)
            ->where('vendor_id', $vendorId)
            ->get();

        if ($addresses->isEmpty()) {
            return response()->json($data);
        }

        foreach ($addresses as $address) {
            $addressData = $this->formatAddressData($address);

            // Mark primary address
            if ($address->is_default && $user) {
                $addressData['name']  = $user->name;
                $addressData['email'] = $user->email;
                $addressData['phone'] = $user->phone;
                $data['primary_address'] = $addressData;
            }

            $data['all_addresses'][] = $addressData;
        }

        return response()->json($data);
    }

    /**
     * Format a single address with country and state names.
     */
    private function formatAddressData(CustomerAddress $address): array
    {
        $addressData = Arr::only($address->toArray(), [
            'id', 'customer_id', 'company_name', 'type_of_place',
            'address_1', 'address_2', 'city', 'state', 'zip', 'country',
            'is_default', 'created_at', 'updated_at',
        ]);

        $addressData['country_name'] = Country::where('code', $address->country)->value('name');
        $addressData['state_name']   = Division::getStateNameByCountryStateCode(
            $address->country,
            $address->state
        );

        return $addressData;
    }
}
