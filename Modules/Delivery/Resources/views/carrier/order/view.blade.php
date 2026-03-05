@extends('delivery::layouts.app')
@section('page_title', __('Order Details'))
@section('css')
    <!-- select2 css -->
    <link rel="stylesheet" href="{{ asset('public/datta-able/plugins/select2/css/select2.min.css') }}">
@endsection
@section('content')

    <!-- Main content -->
    <div class="col-sm-12 order-details-container" id="invoice-view-container">
        <div>
            <div class="card card-width">
                <div class="card-header">
                    <div class="d-flex flex-md-row flex-column justify-content-md-between">
                        <h6 class="order-details-text text-uppercase"> <a
                                href="{{ route('order.view', $order->id) }}">{{ __('Order') }} </a> {{ __('Details') }}
                        </h6>
                        <div>
                            <span class="order-number">{{ __('Reference') }}</span>
                            <h6 class="order-reference"><span>#{{ $order->reference }}</span></h6>
                        </div>
                    </div>
                    <div class="order-details-body">
                        <div>
                            <div class="status-dropdown col-md-3 mb-4">
                                <p>{{ __('Payment Status') }}</p>
                                <span class="font-bold">{{ __($order->payment_status) }}</span>
                            </div>
                            @if (optional($order->paymentMethod)->gateway != null)
                                <p class="payment-method">{{ __('Payment Method') }} <span
                                        class="order-detail-payment-gap">:</span> <span
                                        class="payment-type">{{ paymentRenamed(optional($order->paymentMethod)->gateway) }}</span>
                                </p>
                            @endif
                            @if ($order->paid > 0 && !empty(optional($order->transaction)->transaction_date))
                                <p class="paid-on">{{ __('Paid On') }} <span class="order-detail-paid-gap">:</span> <span
                                        class="payment-date">{{ formatDate(optional($order->transaction)->transaction_date) }}</span>
                                    @if (!empty($order->TransactionId($order->id)))
                                        <a
                                            href="{{ route('transaction.edit', $order->TransactionId($order->id)) }}">({{ __('View Transaction') }})</a>
                                    @endif
                                </p>
                            @endif

                            <div class="d-md-flex gbs-section">
                                <div class="general-section">
                                    <p class="text-uppercase general">{{ __('General') }}</p>
                                    <div>
                                        <span class="date-created">{{ __('Order Date') }}</span>

                                        <br>
                                        <div class="d-flex date-summary">
                                            <p>{{ $order->order_date }}</p>
                                        </div>
                                        <div class="status-dropdown">
                                            <p>{{ __('Status') }}</p>

                                            @if ($order->order_status_id == getOrderStatusId('delivered'))
                                                <span>{{ __('Delivered') }}</span>
                                            @elseif($order->order_status_id == getOrderStatusId('completed'))
                                                <span>{{ __('Completed') }}</span>
                                            @else
                                                <select class="form-control select2" name="status" id="status">
                                                    @foreach ($orderStatus as $status) 
                                                        <option value="{{ $status->id }}"
                                                                {{ $order->order_status_id == $status->id ? 'selected' : '' }}>
                                                                {{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div class="customer-dropdown">
                                            <p>{{ __('Customers') }}</p>
                                            <p> {{ optional($order->user)->name ?? __('Guest') }} </p>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $shippingAddress = $order->getShippingAddress();
                                    $billingAddress = $order->getBillingAddress();
                                @endphp
                                <div class="billing-section">
                                    <div class="billing-icon-container text-uppercase">
                                        <p class="billing">{{ __('Billing Address') }}</p>
                                    </div>
                                    <div class="billing-information-container">
                                        <p class="billing-information"> {{ __('Name') }}: <span>
                                                {{ $billingAddress->first_name }} {{ $billingAddress->last_name }}</span>
                                        </p>
                                        <p class="billing-information"> {{ __('Email') }}: <span>
                                                {{ $billingAddress->email }} </span> </p>
                                        <p class="billing-information"> {{ __('Phone') }}: <span>
                                                {{ $billingAddress->phone }} </span> </p>
                                        <p class="billing-information"> {{ __('Address') }}: <span>
                                                {{ $billingAddress->address_1 }}
                                                {{ !empty($billingAddress->address_2) ? ', ' . $billingAddress->address_2 : '' }},
                                                {{ $billingAddress->city }} </span> </p>
                                        <p class="billing-information"> {{ __('Postcode') . '/' . __('ZIP') }}: <span>
                                                {{ $billingAddress->zip }} </span> </p>
                                        <p class="billing-information"> {{ __('State') }}: <span>
                                                {{ $billingAddress->state }} </span> </p>
                                        <p class="billing-information"> {{ __('Country') }}: <span>
                                                {{ $billingAddress->country }} </span> </p>
                                    </div>
                                </div>

                                <div class="shipping-section">
                                    <div class="shipping-icon-container text-uppercase">
                                        <p class="shipping">{{ __('Shipping Address') }}</p>
                                    </div>

                                    <div class="billing-information-container">
                                        <p class="billing-information"> {{ __('Name') }}: <span>
                                                {{ $shippingAddress->first_name . ' ' . $shippingAddress->last_name }}
                                            </span> </p>
                                        <p class="billing-information"> {{ __('Address') }}: <span>
                                                {{ $shippingAddress->address_1 }}
                                                {{ !empty($shippingAddress->address_2) ? ', ' . $shippingAddress->address_2 : '' }},
                                                {{ $shippingAddress->city }} </span> </p>
                                        <p class="billing-information"> {{ __('Postcode') . '/' . __('ZIP') }}: <span>
                                                {{ $shippingAddress->zip }} </span> </p>
                                        <p class="billing-information"> {{ __('State') }}: <span>
                                                {{ $shippingAddress->state }} </span> </p>
                                        <p class="billing-information"> {{ __('Country') }}: <span>
                                                {{ $shippingAddress->country }} </span> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-width">
                <div class="col-sm-12 form-tabs">
                    <div class="main-body order-info-container">
                        <div class="page-wrapper">
                            <div class="row">
                                <div id="printTable">
                                    <div class="row m-0 order-info-table-container">
                                        <div class="col-sm-12 order-info-table">
                                            <div class="table-responsive order-details-table-responsive">
                                                <table class="table invoice-detail-table">
                                                    <thead>
                                                        @if (isActive('Shop'))                                                         
                                                            @php $shop = true; @endphp
                                                        @endif
                                                        <tr class="thead-default order-info-thead">
                                                            <th>{{ __('') }}</th>
                                                            <th>{{ __('Products') }}</th>
                                                            <th>{{ __('SKU') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Cost') }}</th>
                                                            <th>{{ __('Qty') }}</th>
                                                            <th>{{ __('Total') }}</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($orderDetails as $details)
                                                            @if (!is_null($details[0]->vendor_id))
                                                                <tr>
                                                                    <td colspan="5" class="pl-31p general">
                                                                        {{ optional($details[0]->vendor)->name }}
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            @foreach ($details as $detail)
                                                                @php
                                                                    if (isActive('Refund')) {
                                                                        $orderDeliverId = \App\Models\Order::getFinalOrderStatus();
                                                                    }
                                                                    
                                                                    $opName = '';
                                                                    if ($detail->payloads != null) {
                                                                        $option = (array) json_decode($detail->payloads);
                                                                        $itemCount = count($option);
                                                                        $i = 0;
                                                                        foreach ($option as $key => $value) {
                                                                            $opName .= $key . ': ' . $value . (++$i == $itemCount ? '' : ', ');
                                                                        }
                                                                    }
                                                                    
                                                                    $productInfo = $orderAction->getProductInfo($detail);
                                                                @endphp
                                                                <tr>
                                                                    <td></td>
                                                                    <td>
                                                                        <div class="d-flex">
                                                                            <div class="order-itm-img-con">
                                                                                <img src="{{ $productInfo['image'] }}"
                                                                                    alt="{{ __('Image') }}">
                                                                            </div>
                                                                            <div class="order-item-name-attribute">
                                                                                <h6>
                                                                                    <a class="order-item-name mt-9 d-block"
                                                                                        href="{{ $productInfo['url'] }}"
                                                                                        title="{{ $detail->product_name }}">
                                                                                        {{ trimWords($detail->product_name, 25) }}
                                                                                        <br>
                                                                                    </a>
                                                                                </h6>
                                                                                <p class="order-item-attr">
                                                                                    {{ $opName }} </p>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            title="{{ optional($detail->product)->sku }}">{{ trimWords(optional($detail->product)->sku, 10) }}</span>
                                                                    </td>
                                                                    @php
                                                                        $totalRefund = $detail
                                                                            ->refunds()
                                                                            ->where('status', 'Accepted')
                                                                            ->sum('quantity_sent');
                                                                    @endphp
                                                                    <td>
                                                                        @if ($totalRefund != $detail->quantity)
                                                                            @if ($detail->is_delivery == 1)
                                                                                <span
                                                                                    class="d-block mt-22p">{{ __('Completed') }}</span>
                                                                            @else
                                                                                @foreach ($orderStatus as $status)
                                                                                    @if ($detail->order_status_id == $status->id)
                                                                                        <span class="d-block mt-22p">
                                                                                            {{ $status->name }}
                                                                                        </span>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                        @else
                                                                            <span
                                                                                class="d-block mt-22p">{{ __('Refunded') }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ formatCurrencyAmount($detail->price) }}
                                                                    </td>
                                                                    <td class="d-flex">
                                                                        <span class="order-q-icon">x</span>
                                                                        {{ formatCurrencyAmount($detail->quantity) }}
                                                                    </td>
                                                                    <td>{{ formatNumber($detail->price * $detail->quantity, optional($order->currency)->symbol) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 invoice-table-container ">
                                            <table
                                                class="table table-responsive invoice-table invoice-total invoice-total-customize table-spa">
                                                <tbody class="total-amount-design">
                                                    @php
                                                        $couponOffer = isset($order->couponRedeems) && $order->couponRedeems->sum('discount_amount') > 0 && isActive('Coupon') ? $order->couponRedeems->sum('discount_amount') : 0;
                                                    @endphp
                                                    <tr>
                                                        <th>{{ __('Sub Total') }} :</th>
                                                        <td class="text-right">
                                                            {{ formatNumber($order->total + $order->other_discount_amount + $couponOffer - ($order->shipping_charge + $order->tax_charge), optional($order->currency)->symbol) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="py-3">{{ __('Shipping') }}
                                                            {{ !is_null($order->shipping_title) ? '( ' . $order->shipping_title . ' )' : null }}
                                                            :</th>
                                                        <td class="py-3 text-right">
                                                            {{ formatNumber($order->shipping_charge, optional($order->currency)->symbol) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Tax') }} :</th>
                                                        <td class="text-right">
                                                            {{ formatNumber($order->tax_charge, optional($order->currency)->symbol) }}
                                                        </td>
                                                    </tr>
                                                    @if ($couponOffer > 0)
                                                        <tr>
                                                            <th class="py-3">{{ __('Coupon offer') }} :</th>
                                                            <td class="py-3 text-right">
                                                                {{ formatNumber($couponOffer, optional($order->currency)->symbol) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if ($order->other_discount_amount > 0)
                                                        <tr>
                                                            <th class="py-3">{{ __('Discount') }} :</th>
                                                            <td class="py-3 text-right">
                                                                {{ formatNumber($order->other_discount_amount, optional($order->currency)->symbol) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr class="text-info">
                                                        <td>
                                                            <hr />
                                                            <h5 class="order-grand-total">{{ __('Grand Total') }}
                                                                :</h5>
                                                        </td>
                                                        <td>
                                                            <hr />
                                                            <h5 class="order-grand-currency">
                                                                {{ formatNumber($order->total, optional($order->currency)->symbol) }}
                                                            </h5>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-actions-container">
            <div class="card">
                <div class="order-sections-header accordion cursor_pointer">
                    <span>{{ __('Order') }} {{ __('Actions') }}</span>
                    <span class="order-icon drop-down-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                            <path d="M3.33579 4.92324L0.159846 1.11968C-0.211416 0.675046 0.105388 -4.81444e-07 0.685319 -5.06793e-07L6.31468 -7.52861e-07C6.89461 -7.7821e-07 7.21142 0.675045 6.84015 1.11968L3.66421 4.92324C3.57875 5.02559 3.42125 5.02559 3.33579 4.92324Z" fill="#2C2C2C"/>
                        </svg>
                    </span>
                </div>
                <div class="order-sections-body">
                    <div class="trash-update border-top">
                        <button class="w-100" id="update-order" {{ in_array($order->order_status_id, getOrderStatusIds(['delivered', 'completed'])) ? 'disabled' : '' }} class="mt-9">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>

            @php
                $orderDeliverId = \App\Models\Order::getFinalOrderStatus();
                $deliveryDate = $order->deliveryDate($order->id, $orderDeliverId);
            @endphp
            @if(!empty($deliveryDate))
                <div class="card">
                    <div class="order-sections-header accordion cursor_pointer">
                        <span>{{ __('Delivery Time') }}</span>
                        <span class="order-icon drop-down-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                <path d="M3.33579 4.92324L0.159846 1.11968C-0.211416 0.675046 0.105388 -4.81444e-07 0.685319 -5.06793e-07L6.31468 -7.52861e-07C6.89461 -7.7821e-07 7.21142 0.675045 6.84015 1.11968L3.66421 4.92324C3.57875 5.02559 3.42125 5.02559 3.33579 4.92324Z" fill="#2C2C2C"/>
                            </svg>
                        </span>
                    </div>
                    <div class="order-delivery-sections-body">
                        <div>
                            <span class="order-date-text">{{ __('Delivery date') }}</span>
                            <input id="deliveryDate" type="text" class="form-control inputFieldDesign" value="{{ $deliveryDate }}">
                            <br>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="order-sections-header accordion cursor_pointer">
                    <span>{{ __('Create PDF') }}</span>
                    <span class="order-icon drop-down-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                            <path d="M3.33579 4.92324L0.159846 1.11968C-0.211416 0.675046 0.105388 -4.81444e-07 0.685319 -5.06793e-07L6.31468 -7.52861e-07C6.89461 -7.7821e-07 7.21142 0.675045 6.84015 1.11968L3.66421 4.92324C3.57875 5.02559 3.42125 5.02559 3.33579 4.92324Z" fill="#2C2C2C"/>
                        </svg>
                    </span>
                </div>
                <div class="order-pdf-btn">
                    <a href="{{ route('carrier.order_print',[$order->id, 'type' => 'pdf']) }}"><button class="pdf-inv-btn">{{ __('PDF Invoice') }}</button></a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var orderId = "{{ $order->id }}";
        var paymentStatus = "{{ $order->payment_status }}";
        var finalOrderStatus = "{{ $finalOrderStatus }}";
        var orderUrl = "{{ route('carrier.change_status') }}";
        var orderView = "admin";
    </script>
    <script src="{{ asset('public/dist/js/custom/common.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/jquery.blockUI.min.js') }}"></script>
    <!-- select2 JS -->
    <script src="{{ asset('public/datta-able/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('Modules/Delivery/Resources/assets/js/carrier-order.min.js') }}"></script>
@endsection
