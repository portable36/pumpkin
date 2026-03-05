<?php

namespace App\Services\Order;

use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderMeta;
use App\Models\OrderNoteHistory;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Actions\OrderAction;
use App\Services\Product\AddToCartService;
use Illuminate\Support\Facades\DB;
use Modules\Commission\Http\Models\Commission;
use Modules\Commission\Http\Models\OrderCommission;
use Modules\Coupon\Http\Models\Coupon;
use Modules\Gateway\Entities\PaymentLog;
use Modules\Inventory\Entities\StockManagement;
use Modules\Refund\Entities\RefundReason;

class InvoiceService
{
    private $order;

    private $request;

    private $requestUser;

    public function __construct($request = null, $requestUser = 'admin')
    {
        $this->request = $request;
        $this->order = Order::where('id', $request->order_id)->first();
        $this->requestUser = $requestUser;
    }

    /**
     * invoice customize
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function customize($statusCheck = true)
    {
        $request = $this->request;
        $order = $this->order;
        $msg = __('The :x has been successfully deleted.', ['x' => __('Data')]);

        if (empty($order) || $statusCheck && $order->orderStatus?->payment_scenario == 'paid') {
            return $this->redirectPage($order, __('Something went wrong, please try again.'));
        }

        if (isset($request->action) && $request->action == 'delete' && $request->type == 'product') {
            $response = $order->deleteProductFromOrder($request);

            if ($response) {

                return $this->redirectPage($order, $msg);
            }
        }

        if (isset($request->action) && $request->action == 'delete' && $request->type == 'fee') {
            $response = $order->deleteFeeFromOrder($request);

            if ($response) {

                return $this->redirectPage($order, $msg);
            }
        }

        if (isset($request->action) && $request->action == 'delete' && $request->type == 'custom_tax') {
            $response = $order->deleteCustomTaxFromOrder($request);

            if ($response) {

                return $this->redirectPage($order, $msg);
            }
        }

        if (isset($request->action) && $request->action == 'edit') {

            parse_str($request->data, $inputData);

            if (is_array($inputData) && count($inputData) > 0) {
                $response = $order->updateProductValueFromOrder($request, $inputData);

                if (isset($response['status']) && $response['status']) {
                    return $this->redirectPage($order);
                } else {
                    return $this->redirectPage($order, $response['message']);
                }
            }
        }

        if (isset($request->action) && $request->action == 'add_fee') {
            $response = $order->addFeeInOrder($request);

            if ($response) {

                return $this->redirectPage($order);
            }
        }

        if (isset($request->action) && $request->action == 'add_tax') {
            $response = $order->addCustomTaxInOrder($request);

            if ($response) {

                return $this->redirectPage($order);
            }
        }

        if (isset($request->action) && $request->action == 'add_coupon') {
            $request['request_user'] = $this->requestUser;
            $response = $order->addCouponInOrder($request);

            if ($response) {

                if (isset($response['message'])) {
                    return $this->redirectPage($order, $response['message']);
                } else {
                    return $this->redirectPage($order);
                }
            }
        }

        if (isset($request->action) && $request->action == 'delete' && $request->type == 'coupon_delete') {
            $response = $order->deleteCouponFromOrder($request);

            if ($response) {

                return $this->redirectPage($order);
            }
        }

        if (isset($request->product_id) && is_array($request->product_id)) {
            $response = $order->orderCustomization($request);

            if (isset($response['status']) && $response['status']) {
                return $this->redirectPage($order);
            } else {
                return $this->redirectPage($order, $response['message']);
            }
        }

        return $this->redirectPage($order, __('Something went wrong, please try again.'));
    }

    /**
     * redirect page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectPage($order, $msg = null)
    {
        $order = Order::where('id', $order->id)->first();
        if (! empty($order)) {
            $data['orderStatus'] = OrderStatus::getAll()->sortBy('order_by');
            $data['order'] = $order;
            $data['orderDetails'] = $order->orderDetails?->groupBy('vendor_id');
            $data['refundReasons'] = RefundReason::where('status', 'Active')->get();
            $data['orderStatusHistories'] = OrderStatusHistory::where('order_id', $order->id)->whereNotNull('product_id')->orderByDesc('id')->get();
            $data['finalOrderStatus'] = OrderStatus::orderBy('order_by', 'DESC')->first()->id;
            $data['orderNotes'] = OrderNoteHistory::where(['order_id' => $order->id, 'user_id' => auth()->user()->id])->orderBy('id', 'desc')->get();
            $data['orderAction'] = new OrderAction();
            $data['customFee'] = $order->metadata?->where('key', 'meta_custom_fee')->first();
            $data['customTax'] = $order->metadata?->where('key', 'meta_custom_tax')->first();
            if ($this->requestUser == 'vendor') {
                $data['vendorId'] = session()->get('vendorId');
            }

            return response()->json(
                [
                    'status' => true,
                    'viewHtml' => $this->requestUser == 'vendor' ? view('vendor.orders.view_sections.calculation', $data)->render() : view('admin.orders.view_sections.calculation', $data)->render(),
                    'message' => is_null($msg) ? __('The :x has been successfully saved.', ['x' => __('Data')]) : $msg,
                ]
            );
        }
    }

    public function draftOrder()
    {
        $orderStatus = OrderStatus::where('slug', 'draft')->first();

        if (empty($orderStatus)) {
            $orderStatus = OrderStatus::where('slug', 'pending-payment')->first();
        }

        $data = [
            'id' => null,
            'user_id' => null,
            'reference' => Order::getOrderReference(preference('order_prefix', null)),
            'note' => auth()->user()->id,
            'order_date' => date('Y-m-d'),
            'currency_id' => preference('dflt_currency_id'),
            'leave_door' => null,
            'other_discount_amount' => null,
            'shipping_charge' => null,
            'tax_charge' => null,
            'shipping_title' => null,
            'total' => 0,
            'paid' => 0,
            'total_quantity' => 0,
            'amount_received' => null,
            'order_status_id' => $orderStatus->id ?? null,
        ];

        $orderId = (new Order())->store($data);

        $formatData[] = [
            'order_id' => $orderId,
            'type' => 'string',
            'key' => 'track_code',
            'value' => strtoupper(\Str::random(10)),
        ];

        OrderMeta::upsert($formatData, ['order_id', 'key']);

        return $orderId;

    }

    /**
     * total Custom Amount
     *
     * @return int|mixed
     */
    public static function totalCustomAmount($order, $vendorOnly = false)
    {
        $feeTotal = 0;
        $customTaxTotal = 0;
        $customFee = $order->metadata?->where('key', 'meta_custom_fee')->first();
        $customTax = $order->updatedCustomTaxFee($order, true, $vendorOnly);
        if (! empty($customFee)) {
            $feeData = json_decode($customFee->value);
            foreach ($feeData as $feeKey => $fee) {
                $feeTotal += $fee->calculated_amount;
                $customTaxTotal += $fee->tax;
            }
        }

        return $feeTotal + $customTaxTotal + $customTax;
    }

    /**
     * add to cart
     *
     * @return bool
     */
    public function addToCart()
    {
        \Cart::destroy(null, 'multiple');

        foreach ($this->request->products ?? [] as $data) {
            $product = Product::find($data['id']);
            $isVariableProduct = false;
            if (! is_null($product->parent_id)) {
                $variation = $product;
                $product = Product::find($product->parent_id);
                $isVariableProduct = true;
            }

            \Cart::add(
                [
                    'id' => $isVariableProduct ? $variation->id : $product->id,
                    'code' => $isVariableProduct ? $variation->code : $product->code,
                    'slug' => $product->slug,
                    'vendor_id' => $product->vendor_id,
                    'shop_id' => $product->shop_id,
                    'name' => $product->name,
                    'quantity' => $data['quantity'] ?? 1,
                    'price' => $data['price'],
                    'actual_price' => $isVariableProduct ? $variation->regular_price : $product->regular_price,
                    'photo' => null,
                    'parent_id' => $isVariableProduct ? $variation->parent_id : null,
                    'parent_code' => $isVariableProduct ? $product->code : null,
                    'parent_slug' => $isVariableProduct ? $product->slug : null,
                    'variation_id' => $isVariableProduct ? $variation->id : null,
                    'variation_photo' => null,
                    'variation_meta' => null,
                    'type' => $product->type,
                    'is_individual_sale' => 0,
                ]
            );
        }

        return true;
    }

    /**
     * get tax shipping
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxShipping()
    {
        \Cart::checkCartData();
        \Cart::selectedCartCollection();

        $address = (object) [
            'country' => $this->request?->address['country'] ?? null,
            'state' => $this->request?->address['state'] ?? null,
            'city' => $this->request?->address['city'] ?? null,
            'post_code' => $this->request?->address['zip'] ?? null,
        ];

        $cartService = new AddToCartService();
        $cartService->destroyShippingIndex();
        $taxShipping = $cartService->getTaxShipping($address, null, true);

        return response()->json([
            'status' => true,
            'tax_shipping' => $taxShipping,
        ]);
    }

    /**
     * total tax amount
     *
     * @return int
     */
    private function totalTaxAmount()
    {
        $amount = 0;
        foreach ($this->request->tax as $tax) {
            $amount += $tax['amount'];
        }

        return $amount;
    }

    /**
     * total fee amount
     *
     * @return int
     */
    private function totalFee()
    {
        $amount = 0;
        foreach ($this->request->fee as $fee) {
            $amount += $fee['amount'];
        }

        return $amount;
    }

    /**
     * total coupon amount
     *
     * @return int
     */
    private function totalCoupon()
    {
        $amount = 0;
        foreach ($this->request->coupon ?? [] as $coupon) {
            $amount += $coupon['amount'];
        }

        return $amount;
    }

    /**
     * Calculate the total amount for an order.
     *
     * This function computes the total amount by summing up the subtotal,
     * shipping, tax, and fee amounts, and then subtracting the coupon and
     * discount amounts from the total.
     *
     * @return float The calculated total amount for the order.
     */
    private function totalAmount()
    {
        $subTotal = $this->request?->subTotal;
        $shipping = $this->request?->shippingAmount;
        $tax = $this->totalTaxAmount();
        $fee = $this->totalFee();
        $coupon = $this->totalCoupon();
        $discount = $this->request?->discount;

        return $subTotal + $shipping + $tax + $fee - $coupon - $discount;
    }

    /**
     * Calculate the total quantity of products in the request.
     *
     * This function iterates over the product data in the request and sums up
     * the quantities for each product, returning the total quantity.
     *
     * @return int The total quantity of products.
     */
    private function totalQuantity()
    {
        $quantity = 0;
        foreach ($this->request->productData as $product) {
            $quantity += $product['quantity'];
        }

        return $quantity;
    }

    /**
     * Update the order meta information.
     *
     * This function takes an order id and an array of download data, and
     * updates the order meta information in the database. The function
     * first formats the data into an array of order meta objects, and then
     * uses the upsert method to update the order meta table. If the upsert
     * is successful, the function then updates the download data for the
     * order, and returns true. If the upsert fails, the function returns
     * false.
     *
     * @param  int  $orderId  The id of the order to update.
     * @param  array  $downloadData  The array of download data to update.
     * @return bool True if the update was successful, false otherwise.
     */
    private function updateMeta($orderId, $downloadData)
    {
        $billing = [
            'billing_address_first_name' => $this->request->billingAddress['firstName'] ?? '',
            'billing_address_last_name' => $this->request->billingAddress['lastName'] ?? '',
            'billing_address_phone' => $this->request->billingAddress['phone'] ?? '',
            'billing_address_email' => $this->request->billingAddress['email'] ?? '',
            'billing_address_company_name' => $this->request->billingAddress['companyName'] ?? '',
            'billing_address_city' => $this->request->billingAddress['city'] ?? '',
            'billing_address_state' => $this->request->billingAddress['state'] ?? '',
            'billing_address_zip' => $this->request->billingAddress['zip'] ?? '',
            'billing_address_country' => $this->request->billingAddress['country'] ?? '',
            'billing_address_type_of_place' => $this->request->billingAddress['type_of_place'] ?? '',
            'billing_address_address_1' => $this->request->billingAddress['address1'] ?? '',
            'billing_address_address_2' => $this->request->billingAddress['address2'] ?? '',
            'billing_address_created_at' => now(),
            'billing_address_updated_at' => now(),
        ];

        $shipping = [
            'shipping_address_first_name' => $this->request->shippingAddress['firstName'] ?? $this->request->billingAddress['firstName'] ?? '',
            'shipping_address_last_name' => $this->request->shippingAddress['lastName'] ?? $this->request->billingAddress['lastName'] ?? '',
            'shipping_address_phone' => $this->request->shippingAddress['phone'] ?? $this->request->billingAddress['phone'] ?? '',
            'shipping_address_email' => $this->request->shippingAddress['email'] ?? $this->request->billingAddress['email'] ?? '',
            'shipping_address_company_name' => $this->request->shippingAddress['companyName'] ?? $this->request->billingAddress['companyName'] ?? '',
            'shipping_address_city' => $this->request->shippingAddress['city'] ?? $this->request->billingAddress['city'] ?? '',
            'shipping_address_state' => $this->request->shippingAddress['state'] ?? $this->request->billingAddress['state'] ?? '',
            'shipping_address_zip' => $this->request->shippingAddress['zip'] ?? $this->request->billingAddress['zip'] ?? '',
            'shipping_address_country' => $this->request->shippingAddress['country'] ?? $this->request->billingAddress['country'] ?? '',
            'shipping_address_type_of_place' => $this->request->shippingAddress['type_of_place'] ?? $this->request->billingAddress['type_of_place'] ?? '',
            'shipping_address_address_1' => $this->request->shippingAddress['address1'] ?? $this->request->billingAddress['address1'] ?? '',
            'shipping_address_address_2' => $this->request->shippingAddress['address2'] ?? $this->request->billingAddress['address2'] ?? '',
            'shipping_address_created_at' => now(),
            'shipping_address_updated_at' => now(),
        ];

        $address = array_merge($billing, $shipping);

        $formatData = collect($address)->map(function ($value, $key) use ($orderId) {
            return [
                'order_id' => $orderId,
                'type'     => 'string',
                'key'      => $key,
                'value'    => $value,
            ];
        });

        $extraData = collect([
            ['key' => 'track_code',     'value' => strtoupper(\Str::random(10))],
            ['key' => 'location_id',    'value' => $this->request->locationId ?? null],
            ['key' => 'customer_note',  'value' => $this->request->customerNote ?? null],
            ['key' => 'fee',            'value' => json_encode($this->request->fee) ?? null],
            ['key' => 'shipping_name',  'value' => $this->request->shippingName ?? null],
            ['key' => 'tax',            'value' => json_encode($this->request->tax) ?? null],
        ])->map(function ($item) use ($orderId) {
            return [
                'order_id' => $orderId,
                'type'     => 'string',
                'key'      => $item['key'],
                'value'    => $item['value'],
            ];
        })->toArray();

        $formatData = $formatData->merge($extraData)->toArray();

        if (OrderMeta::upsert($formatData, ['order_id', 'key'])) {
            if (is_array($downloadData) && count($downloadData) > 0) {
                OrderMeta::updateOrCreate(
                    ['order_id' => $orderId, 'key' => 'download_data'],
                    ['type' => 'array', 'value' => $downloadData]
                );
            }

            return true;
        }

        return false;
    }

    /**
     * Store commission for each product in an order
     *
     * @param  int  $orderId
     * @return void
     */
    private function storeCommission($orderId)
    {
        // commission
        $commission = Commission::getAll()->first();
        if (! empty($commission) && $commission->is_active == 1) {
            $orderDetails = OrderDetail::where('order_id', $orderId)->get();
            $orderCommission = [];
            foreach ($orderDetails as $details) {
                if (isset($details->vendor->sell_commissions) && optional($details->vendor)->sell_commissions > 0) {
                    $orderCommission[] = [
                        'order_details_id' => $details->id,
                        'category_id' => null,
                        'vendor_id' => $details->vendor_id,
                        'amount' => $details->vendor?->sell_commissions,
                        'status' => 'Pending',
                    ];
                } elseif ($commission->is_category_based == 1 && isset($details->productCategory->category->sell_commissions) && ! empty($details->productCategory->category->sell_commissions) && $details->productCategory->category->sell_commissions > 0) {
                    $orderCommission[] = [
                        'order_details_id' => $details->id,
                        'category_id' => $details->productCategory?->category_id,
                        'vendor_id' => null,
                        'amount' => $details->productCategory?->category?->sell_commissions,
                        'status' => 'Pending',
                    ];
                } else {
                    $orderCommission[] = [
                        'order_details_id' => $details->id,
                        'category_id' => $details->productCategory->category_id ?? null,
                        'vendor_id' => $details->vendor_id ?? null,
                        'amount' => $commission->amount,
                        'status' => 'Pending',
                    ];
                }
            }
            if (is_array($orderCommission) && count($orderCommission) > 0) {
                (new OrderCommission())->store($orderCommission);
            }
        }
    }

    /**
     * Save Invoice
     *
     * @return \Illuminate\Http\Response
     */
    public function save()
    {
        $defaultCurrency = Currency::getDefault();
        $orderStatus = OrderStatus::find($this->request->status);
        $reference = Order::getOrderReference(preference('order_prefix', null));

        $order = [];
        $order['user_id'] = null;
        $order['customer_id'] = $this->request->userId == 'Guest' || $this->request->userId == '' ? null : $this->request->userId;
        $order['order_date'] = DbDateFormat($this->request->orderDate);
        $order['currency_id'] = $defaultCurrency->id;
        $order['shipping_charge'] = $this->request->shippingAmount;
        $order['shipping_title'] = $this->request->shippingName ?? '';
        $order['tax_charge'] = $this->totalTaxAmount();
        $order['total'] = $this->totalAmount();
        $order['total_quantity'] = $this->totalQuantity();
        $order['paid'] = $orderStatus?->payment_scenario == 'paid' ? $this->totalAmount() : 0;
        $order['amount_received'] = $order['paid'];
        $order['other_discount_amount'] = $this->request->discount ?? null;
        $order['order_status_id'] = $this->request->status;
        $order['payment_status'] = $orderStatus?->payment_scenario == 'paid' ? 'Paid' : 'Unpaid';
        $order['reference'] = $reference;
        $order['channel'] = 'invoice';

        DB::beginTransaction();

        try {
            $orderId = (new Order())->store($order);

            if (! $orderId) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => __('Order not created'),
                ]);
            }

            $history['order_id'] = $orderId;
            $history['order_status_id'] = $this->request->status;

            (new OrderStatusHistory())->store($history);

            $downloadable = [];

            foreach ($this->request->productData as $key => $cart) {
                $item = Product::where('id', $cart['id'])->published()->first();
                $unit = $item->unit;
                if ($item->meta_downloadable == 1) {
                    $idCount = 1;
                    foreach ($item->meta_downloadable_files as $files) {
                        if (isset($files['url']) && ! empty($files['url'])) {
                            $url = urlSlashReplace($files['url'], ['\/', '\\']);
                            $downloadable[] = [
                                'id' => $idCount++,
                                'download_limit' => ! is_null($item->meta_download_limit) && $item->meta_download_limit != '' && $item->meta_download_limit != '-1' ? $item->meta_download_limit * $cart['quantity'] : $item->meta_download_limit,
                                'download_expiry' => $item->meta_download_expiry,
                                'link' => $url,
                                'download_times' => 0,
                                'is_accessible' => 1,
                                'vendor_id' => $item->vendor_id,
                                'name' => $item->name,
                                'f_name' => $files['name'],
                            ];
                        }
                    }
                }

                $variationMeta = null;
                if ($item['type'] == 'Variation') {
                    $input = $cart['variation_meta'];

                    $trimmed = trim($input, '()');
                    $pairs = explode(',', $trimmed);

                    $result = [];

                    foreach ($pairs as $pair) {
                        [$key, $value] = array_map('trim', explode(':', $pair, 2));
                        $result[$key] = $value;
                    }

                    $variationMeta = json_encode($result);
                }

                /* Check Inventory & update */
                if (! $item->checkInventory($cart['quantity'], $item->meta_backorder, $orderStatus->slug)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => __('Invalid Order!'),
                    ]);
                }
                /* End Inventory & update */

                $tax = 0;

                $detailData[] = [
                    'product_id' => $cart['id'],
                    'parent_id' => $item['parent_id'],
                    'order_id' => $orderId,
                    'vendor_id' => $this->request->vendorId,
                    'shop_id' => $item['shop_id'],
                    'product_name' => $item['name'],
                    'unit' => $unit ?? defaultUnit()?->abbr,
                    'description' => $cart['description'],
                    'price' => $cart['price'],
                    'quantity_sent' => $orderStatus?->payment_scenario == 'paid' ? $cart['quantity'] : 0,
                    'quantity' => $cart['quantity'],
                    'order_status_id' => $orderStatus->id,
                    'payloads' => $variationMeta,
                    'order_by' => $key,
                    'shipping_charge' => null,
                    'tax_charge' => $tax,
                    'is_stock_reduce' => $item->isStockReduce($orderStatus->slug),
                    'estimate_delivery' => $item->type == 'Variation' ? $item->parentDetail?->estimated_delivery : $item->estimated_delivery,
                ];

                if ($item->type == 'Variation') {
                    $item->parentDetail?->updateCategorySalesCount();
                } else {
                    $item->updateCategorySalesCount();
                }
            }

            (new OrderDetail())->store($detailData);

            OrderMeta::where('order_id', $orderId)->delete();
            $this->updateMeta($orderId, $downloadable);

            $this->storeCommission($orderId);

            $latestOrder = Order::where('id', $orderId)->first();

            if (isActive('Coupon')) {
                $couponRedem = [];
                if (is_array($this->request->coupon) && count($this->request->coupon) > 0) {
                    $user = User::find($this->request->userId);
                    foreach ($this->request->coupon as $data) {
                        $coupon = Coupon::where('code', $data['code'])->first();
                        $couponRedem[] = [
                            'coupon_id' => $coupon?->id,
                            'coupon_code' => $coupon?->code,
                            'user_id' => $user?->id,
                            'user_name' => $user?->name,
                            'order_id' => $latestOrder->id,
                            'order_code' => $latestOrder->reference,
                            'discount_amount' => $data['amount'],
                        ];
                    }

                    (new \Modules\Coupon\Http\Models\CouponRedeem())->store($couponRedem);
                }
            }

            PaymentLog::create([
                'total' => $this->totalAmount(),
                'currency_code' => $defaultCurrency->name,
                'sending_details' => json_encode($latestOrder),
                'code' => $latestOrder->id,
                'status' => $orderStatus?->payment_scenario == 'paid' ? 'completed' : 'pending',
                'gateway' => $this->request->paymentMethod,
            ]);

            DB::commit();

            // Action hook after vendor create order
            do_action('after_vendor_create_order', $latestOrder);

            return response()->json([
                'status' => true,
                'message' => __('Order has been created successfully'),
                'data' => $latestOrder,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Approve Stock
     *
     * @return void
     */
    private function approveStock($order)
    {
        $order?->orderDetails->each(function ($detail) {
            \Modules\Inventory\Entities\StockManagement::completeDelivery($detail);
        });
    }

    /**
     * Partial Payment
     *
     * @return array
     */
    public function partialPayment($order)
    {
        DB::beginTransaction();

        try {
            $order->paid = $order->paid + $this->request->amount_received;
            $order->amount_received = $order->amount_received + $this->request->amount_received;
            if ($order->amount_received >= $order->total) {
                $orderStatus = OrderStatus::where('slug', 'completed')->first();
                $order->payment_status = 'Paid';
                $order->order_status_id = $orderStatus->id;
                $transactionType = 'Order_payment';
                $this->approveStock($order);
            } else {
                $orderStatus = OrderStatus::where('slug', 'partial-payment')->first();
                $order->payment_status = 'Partial';
                $order->order_status_id = $orderStatus->id;
                $transactionType = 'Order_partial_payment';
            }

            $order->save();

            $orderPartialDataWithActualPrice[] = [
                'user_id' => $order->user_id ?? getUserId(),
                'currency_id' => $order->currency_id,
                'order_id' => $order->id,
                'vendor_id' => $order->orderDetails[0]->vendor_id ?? null,
                'shop_id' => $order->orderDetails[0]->shop_id ?? null,
                'exchange_rate' => optional($order->currency)->exchange_rate,
                'amount' => $this->request->amount_received,
                'paid_amount' => $this->request->amount_received,
                'total_amount' => $order->total,
                'transaction_type' => $transactionType,
                'transaction_date' => $this->request->payment_date,
                'params' => json_encode([
                    'transaction_id' => $this->request->transaction_id,
                    'payment_method' => $this->request->payment_method,
                ]),
                'status' => 'Accepted',
            ];

            (new Transaction())->orderTransactionStore($orderPartialDataWithActualPrice);

            if ($transactionType == 'Order_payment') {
                $log = PaymentLog::where('code', $order->id)->first();
                if ($log) {
                    $log->update([
                        'gateway' => $this->request->payment_method,
                        'status' => 'completed',
                    ]);
                } else {
                    PaymentLog::create([
                        'total' => $order->total,
                        'currency_code' => $order->currency?->name,
                        'sending_details' => json_encode($order),
                        'code' => $order->id,
                        'status' => 'pending',
                        'gateway' => $this->request->payment_name,
                    ]);
                }
            }

            DB::commit();

            return [
                'status' => true,
                'message' => __('Order has been updated successfully'),
                'data' => $order,
            ];
        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'status' => false,
                'message' => $th->getMessage(),
            ];
        }

    }

    /**
     * Update Invoice
     *
     * @return void
     */
    public function updateInvoice()
    {
        $defaultCurrency = Currency::getDefault();
        $orderStatus = OrderStatus::find($this->request->status);

        $order = [];
        $order['user_id'] = null;
        $order['customer_id'] = $this->request->userId == 'Guest' || $this->request->userId == '' ? null : $this->request->userId;
        $order['order_date'] = DbDateFormat($this->request->orderDate);
        $order['currency_id'] = $defaultCurrency->id;
        $order['shipping_charge'] = $this->request->shippingAmount;
        $order['shipping_title'] = $this->request->shippingName ?? '';
        $order['tax_charge'] = $this->totalTaxAmount();
        $order['total'] = $this->totalAmount();
        $order['total_quantity'] = $this->totalQuantity();
        $order['paid'] = $orderStatus?->payment_scenario == 'paid' ? $this->totalAmount() : 0;
        $order['other_discount_amount'] = $this->request->discount ?? null;
        $order['order_status_id'] = $this->request->status;

        DB::beginTransaction();

        try {
            Order::where('id', $this->request->invoiceId)->update($order);
            $orderId = $this->request->invoiceId;
            $history['order_id'] = $orderId;
            $history['order_status_id'] = $this->request->status;

            if (! OrderStatusHistory::where('order_id', $orderId)->where('order_status_id', $this->request->status)->exists()) {
                (new OrderStatusHistory())->store($history);
            }

            $downloadable = [];

            $orderDetails = OrderDetail::where([
                'order_id' => $orderId,
                'is_stock_reduce' => 1,
            ])->get();

            foreach ($orderDetails as $orderDetail) {
                $stock = StockManagement::where([
                    'location_id' => $this->request->locationId,
                    'product_id' => $orderDetail->product_id,
                    'type'        => 'order',
                ])->first();

                if ($stock) {
                    $stock->increment('quantity', $orderDetail->quantity);
                }

                $item = Product::where('id', $orderDetail->product_id)->first();

                if ($item->type == 'Variation') {
                    $item->parentDetail?->decrementCategorySalesCount();
                } else {
                    $item->decrementCategorySalesCount();
                }
            }

            foreach ($this->request->productData as $key => $cart) {
                $item = Product::where('id', $cart['id'])->published()->first();
                $unit = $item->unit;
                if ($item->meta_downloadable == 1) {
                    $idCount = 1;
                    foreach ($item->meta_downloadable_files as $files) {
                        if (isset($files['url']) && ! empty($files['url'])) {
                            $url = urlSlashReplace($files['url'], ['\/', '\\']);
                            $downloadable[] = [
                                'id' => $idCount++,
                                'download_limit' => ! is_null($item->meta_download_limit) && $item->meta_download_limit != '' && $item->meta_download_limit != '-1' ? $item->meta_download_limit * $cart['quantity'] : $item->meta_download_limit,
                                'download_expiry' => $item->meta_download_expiry,
                                'link' => $url,
                                'download_times' => 0,
                                'is_accessible' => 1,
                                'vendor_id' => $item->vendor_id,
                                'name' => $item->name,
                                'f_name' => $files['name'],
                            ];
                        }
                    }
                }

                $variationMeta = null;
                if ($item['type'] == 'Variation') {
                    $input = $cart['variation_meta'];

                    $trimmed = trim($input, '()');
                    $pairs = explode(',', $trimmed);

                    $result = [];

                    foreach ($pairs as $pair) {
                        [$key, $value] = array_map('trim', explode(':', $pair, 2));
                        $result[$key] = $value;
                    }

                    $variationMeta = json_encode($result);
                }

                /* Check Inventory & update */
                if (! $item->checkInventory($cart['quantity'], $item->meta_backorder, $orderStatus->slug)) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => __('Invalid Order!'),
                    ]);
                }
                /* End Inventory & update */

                $tax = 0;

                $detailData[] = [
                    'product_id' => $cart['id'],
                    'parent_id' => $item['parent_id'],
                    'order_id' => $orderId,
                    'vendor_id' => $this->request->vendorId,
                    'shop_id' => $item['shop_id'],
                    'product_name' => $item['name'],
                    'unit' => $unit ?? defaultUnit()?->abbr,
                    'description' => $cart['description'],
                    'price' => $cart['price'],
                    'quantity_sent' => $orderStatus?->payment_scenario == 'paid' ? $cart['quantity'] : 0,
                    'quantity' => $cart['quantity'],
                    'order_status_id' => $orderStatus->id,
                    'payloads' => $variationMeta,
                    'order_by' => $key,
                    'shipping_charge' => null,
                    'tax_charge' => $tax,
                    'is_stock_reduce' => $item->isStockReduce($orderStatus->slug),
                    'estimate_delivery' => $item->type == 'Variation' ? $item->parentDetail?->estimated_delivery : $item->estimated_delivery,
                ];

                if ($item->type == 'Variation') {
                    $item->parentDetail?->updateCategorySalesCount();
                } else {
                    $item->updateCategorySalesCount();
                }
            }
            $latestOrder = Order::where('id', $orderId)->first();

            OrderCommission::whereIn('order_details_id', $latestOrder?->orderDetails->pluck('id') ?? [])->delete();

            OrderDetail::where('order_id', $orderId)->delete();

            (new OrderDetail())->store($detailData);
            $this->updateMeta($orderId, $downloadable);


            $this->storeCommission($orderId);

            if (isActive('Coupon')) {
                \Modules\Coupon\Http\Models\CouponRedeem::where('order_id', $latestOrder->id)->delete();

                $couponRedem = [];
                if (is_array($this->request->coupon) && count($this->request->coupon) > 0) {
                    $user = User::find($this->request->userId);
                    foreach ($this->request->coupon as $data) {
                        $coupon = Coupon::where('code', $data['code'])->first();
                        $couponRedem[] = [
                            'coupon_id' => $coupon?->id,
                            'coupon_code' => $coupon?->code,
                            'user_id' => $user?->id,
                            'user_name' => $user?->name,
                            'order_id' => $latestOrder->id,
                            'order_code' => $latestOrder->reference,
                            'discount_amount' => $data['amount'],
                        ];
                    }

                    (new \Modules\Coupon\Http\Models\CouponRedeem())->store($couponRedem);
                }
            }

            PaymentLog::where('code', $latestOrder->id)->update([
                'total' => $this->totalAmount(),
                'currency_code' => $defaultCurrency->name,
                'sending_details' => json_encode($latestOrder),
                'code' => $latestOrder->id,
                'status' => $orderStatus?->payment_scenario == 'paid' ? 'completed' : 'pending',
                'gateway' => $this->request->paymentMethod,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => __('Order has been created successfully'),
                'data' => $latestOrder,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
