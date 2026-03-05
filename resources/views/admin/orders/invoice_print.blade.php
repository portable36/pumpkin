<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ __('Invoice') }}</title>
    <link rel="stylesheet" href="{{ asset('public/bootstrap/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/css/pdf-invoice.min.css') }}">
</head>

<body>
    @php
        $currency = $order->currency?->name == 'USD' ? $order->currency?->symbol : $order->currency?->name . ' ';
    @endphp
    <div id="invoice-view-container">
        <div class="invoice-header">
            <div class="invoice-header-left">
                @if ($invoiceSetting?->document?->header?->logo == 'logo')
                    <span>
                        <img class="martvill-logo"
                            src="{{ domPdfImageSource($logo) }}">
                    </span>
                @endif

                @if ($invoiceSetting?->document?->header?->logo == 'name')
                    <span>
                        {{ empty($invoiceSetting?->general?->company_name) ? preference('company_name') : $invoiceSetting?->general?->company_name }}
                    </span>
                @endif
            </div>

            <div class="invoice-header-right">
                @if ($invoiceSetting?->document?->header?->is_invoice_no_show)
                    <div>
                        <h2 class="invoice-title">
                            {{ !empty($invoiceSetting?->document?->header?->invoice_label) ? $invoiceSetting?->document?->header?->invoice_label : __('Invoice')  }}
                        </h2>
                        <p class="invoice-id"># {{ $order->reference }}</p>
                        <p class="invoice-date">
                            {{ __('Invoice Date: ') }}{{ $order->order_date }}
                        </p>
                        <p class="invoice-status">
                            <span class="status-text {{ $order->payment_status == 'Paid' ? 'status-paid' : 'status-unpaid' }}">
                                {{ $order->payment_status }}
                            </span>
                            @if (!empty(optional($order->paymentMethod)->gateway))
                                - <span class="gateway-name">{{ optional($order->paymentMethod)->gateway }}</span>
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>


        <table class="billing-table">
            <tr>
                <!-- Bill From -->
                <td class="billing-cell">
                    <p class="billing-title">{{ __('Bill From') }}</p>
                    <p class="billing-name">{{ preference('company_name') }}</p>
                    <p class="billing-text">{{ preference('company_street') }}</p>
                    <p class="billing-text">{{ preference('company_city') }}</p>
                    <p class="billing-text">{{ Modules\GeoLocale\Entities\Country::getNameByCode(preference('company_country')) . ', ' . preference('company_zip_code') }}</p>
                </td>

                <!-- Bill To -->
                <td class="billing-cell">
                    @if ($invoiceSetting?->document?->header?->is_show_customer_info)
                        <p class="billing-title billing-to-offset">{{ __('Bill To') }}</p>
                        @if (isset($user) && !empty($user->name))
                            <p class="billing-name billing-to-offset">{{ $user->name }}</p>
                        @endif
                        @if (isset($user) && !empty($user->phone))
                            <p class="billing-text billing-to-offset">{{ $user->phone }}</p>
                        @endif
                        @if (isset($user) && !empty($user->email))
                            <p class="billing-text billing-to-offset">{{ $user->email }}</p>
                        @endif
                        @php $billingAddress = $order->getBillingAddress(); @endphp
                        <p class="billing-text billing-to-offset">
                            {{ $billingAddress->address_1 . (!empty($billingAddress->address_2) ? ', ' . $billingAddress->address_2 : '') }}
                        </p>
                        @php
                            $addressParts = array_filter([
                                $billingAddress->zip,
                                ucfirst($billingAddress->city),
                                \Modules\GeoLocale\Entities\Division::getStateNameByCountryStateCode($billingAddress->country, $billingAddress->state),
                                \Modules\GeoLocale\Entities\Country::getNameByCode($billingAddress->country),
                            ]);
                        @endphp
                        <p class="billing-text billing-to-offset">{{ implode(', ', $addressParts) }}</p>
                    @endif
                </td>

                <!-- Ship To -->
                <td class="billing-cell ship-to">
                    @if ($invoiceSetting?->document?->delivery_details?->is_delivery_details)
                        @if ($invoiceSetting?->document?->delivery_details?->is_shopping_address)
                            @php $shippingAddress = $order->getShippingAddress(); @endphp
                            <p class="billing-title">{{ __('Ship To') }}</p>
                            <p class="billing-name">{{ $shippingAddress->first_name . ' ' . $shippingAddress->last_name }}</p>
                            <p class="billing-text">{{ $shippingAddress->phone ?? '' }}</p>
                            <p class="billing-text">{{ $shippingAddress->email ?? '' }}</p>
                            <p class="billing-text">
                                {{ $shippingAddress->address_1 . (!empty($shippingAddress->address_2) ? ', ' . $shippingAddress->address_2 : '') }}
                            </p>
                            @php
                                $addressParts = array_filter([
                                    $shippingAddress->zip,
                                    ucfirst($shippingAddress->city),
                                    \Modules\GeoLocale\Entities\Division::getStateNameByCountryStateCode($shippingAddress->country, $shippingAddress->state),
                                    \Modules\GeoLocale\Entities\Country::getNameByCode($shippingAddress->country),
                                ]);
                            @endphp
                            <p class="billing-text">{{ implode(', ', $addressParts) }}</p>
                        @endif
                    @endif
                </td>
            </tr>
        </table>


        <div class="invoice-wrapper">
            <table class="invoice-table">
                <thead>
                    @if (isActive('Shop'))
                        @php $shop = true; @endphp
                    @endif
                    <tr>
                        @if (isActive('SaaS') || !$invoiceSetting?->document?->product_table?->is_image)
                            <th>{{ __('SL') }}</th>
                        @else
                            <th>{{ __('Image') }}</th>
                        @endif

                        <th>
                            {{ empty($invoiceSetting?->document?->product_table?->product_label) ? __('Product Name') : $invoiceSetting?->document?->product_table?->product_label }}
                        </th>
                        <th>{{ __('Unit') }}</th>

                        @if ($shop && $invoiceSetting?->document?->product_table?->is_vendor_name && $order->channel == 'web')
                            <th class="text-center">
                                {{ empty($invoiceSetting?->document?->product_table?->vendor_name_label) ? __('Shop Name') : $invoiceSetting?->document?->product_table?->vendor_name_label }}
                            </th>
                        @endif

                        @if ($invoiceSetting?->document?->product_table?->is_quentity)
                            <th class="text-right">
                                {{ empty($invoiceSetting?->document?->product_table?->quentity_label) ? __('Quantity') : $invoiceSetting?->document?->product_table?->quentity_label }}
                            </th>
                        @endif

                        <th class="text-right">{{ __('Price') }}</th>
                        <th class="text-right">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderDetails as $index => $detail)
                        @php
                            $opName = '';
                            if ($detail->payloads != null) {
                                $option = (array) json_decode($detail->payloads);
                                $opName = implode(',', array_keys($option) ?? null);
                                $opName .= ': ' . implode(',', $option ?? null);
                            }
                            $productInfo = $orderAction->getProductInfo($detail);
                            $isShowIndex = isActive('SaaS') || !$invoiceSetting?->document?->product_table?->is_image;
                        @endphp
                        <tr>
                            <td class="td pdf-product-name-td {{ $isShowIndex ? 'text-center' : '' }} ">
                                @if ($isShowIndex)
                                    {{ $index + 1 }}
                                @else
                                    @php
                                        try {
                                            $productImage = domPdfImageSource($productInfo['image']);
                                        } catch (\Throwable $th) {
                                            $productImage = asset(defaultImage('products'));
                                        }
                                    @endphp
                                    <img class="product-image" src="{{  $productImage }}" />
                                @endif
                            </td>
                            <td class="td pdf-product-name-td">
                                <p>{{ $detail->product_name }}</p>
                                @if ($detail->description)
                                    <p class="product-desc">{{ $detail->description }}</p>

                                @endif
                                @if ($invoiceSetting?->document?->product_table?->is_attribute)
                                    <p class="product-desc">{{ $opName }}</p>
                                @endif
                            </td>
                            <td class="td pdf-product-name-td">{{ $detail->unit ?? $unit->abbr }}</td>
                            @if ($shop && $invoiceSetting?->document?->product_table?->is_vendor_name && $order->channel == 'web')
                                <td class="td text-center pdf-product-name-td">
                                    <p class="product-desc">{{ optional($detail->vendor)->name }}</p>
                                </td>
                            @endif

                            @if ($invoiceSetting?->document?->product_table?->is_quentity)
                                <td class="td text-center pdf-product-name-td">
                                    <p>{{ formatCurrencyAmount($detail->quantity) }}</p>
                                </td>
                            @endif

                            <td class="td text-right pdf-product-name-td">
                                <p>{{ formatNumber($detail->price, $currency) }}</p>
                            </td>

                            <td class="td text-right pdf-product-name-td">
                                <p>{{ formatNumber($detail->price * $detail->quantity, $currency) }}</p>
                            </td>
                        </tr>
                    @endforeach

                    @php
                        $couponOffer = isset($order->couponRedeems) && $order->couponRedeems->sum('discount_amount') > 0 && isActive('Coupon') ? $order->couponRedeems->sum('discount_amount') : 0;
   
                        $feeArray = json_decode($order->fee, true);
                        $totalFee = 0;
                        foreach ($feeArray ?? [] as $item) {
                            $totalFee += (float) $item['amount'];
                        }

                        $colspan = 5;
                        if ($order->channel == 'web') {
                            $colspan = 6;
                        }
                        
                        if (!$invoiceSetting?->document?->product_table?->is_quentity) {
                            $colspan = $colspan - 1;
                        }
                        if (!$invoiceSetting?->document?->product_table?->is_vendor_name && $order->channel == 'web') {
                            $colspan = $colspan - 1;
                        }
                    @endphp
                    <tr>
                        <td colspan="{{ $colspan }}" class="text-right calculation-table">{{ __('Sub Total') }}</td>
                        <td class="text-right calculation-table">
                            {{ formatNumber($order->total + $order->other_discount_amount + $couponOffer - ($order->shipping_charge + $order->tax_charge) - $totalFee, $currency) }}
                        </td>
                    </tr>

                    @if ($totalFee > 0)
                        <tr>
                            <td colspan="{{ $colspan }}" class="text-right calculation-table">{{ __('Fee') }}</td>
                            <td class="text-right calculation-table">
                                {{ formatNumber($totalFee) }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="{{ $colspan }}" class="text-right calculation-table">
                            {{ __('Shipping') }}{{ !is_null($order->shipping_title) ? ' (' . $order->shipping_title . ')' : null }}
                        </td>
                        <td class="text-right calculation-table">
                            {{ formatNumber($order->shipping_charge, $currency) }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="{{ $colspan }}" class="text-right calculation-table">{{ __('Tax') }}</td>
                        <td class="text-right calculation-table">
                            {{ formatNumber($order->tax_charge + $customTax + $customFee['customTaxTotal'], $currency) }}
                        </td>
                    </tr>

                    @if((isset($customFee['feeTotal']) && $customFee['feeTotal']) > 0)
                        <tr>
                            <td colspan="{{ $colspan }}" class="text-right calculation-table">{{ __('Fees') }}</td>
                            <td class="text-right calculation-table">
                                {{ formatNumber($customFee['feeTotal'], $currency) }}
                            </td>
                        </tr>
                    @endif

                    @if ($couponOffer > 0)
                        <tr>
                            <td colspan="{{ $colspan }}" class="text-right calculation-table">{{ __('Coupon offer') }} :</td>
                            <td class="text-right calculation-table">
                                -{{ formatNumber($couponOffer, $currency) }}
                            </td>
                        </tr>
                    @endif
                    @if ($order->other_discount_amount > 0)
                        <tr>
                            <td colspan="{{ $colspan }}" class="text-right calculation-table">{{ __('Discount') }} :</td>
                            <td class="text-right calculation-table">
                                -{{ formatNumber($order->other_discount_amount, $currency) }}
                            </td>
                        </tr>
                    @endif

                    <tr class="total-header">
                        <td colspan="{{ $colspan }}" class="text-right calculation-table total-amount">{{ __('Total') }}</td>
                        <td class="text-right calculation-table total-amount">
                            {{ formatNumber($order->total + $customTax + $customFee['feeTotal'] + $customFee['customTaxTotal'], $currency) }}
                        </td>
                    </tr>

                    @if (strtolower($order->payment_status) == 'partial')
                        @php
                            $grandTotal = $order->total + $customTax + $customFee['feeTotal'] + $customFee['customTaxTotal'];
                            $paidAmount = $order->amount_received ?? 0;
                            $dueAmount = $grandTotal - $paidAmount;
                        @endphp
                        <tr>
                            <td colspan="{{ $colspan }}" class="text-right calculation-table">{{ __('Paid Amount') }}</td>
                            <td class="text-right calculation-table">
                                {{ formatNumber($paidAmount, $currency) }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="{{ $colspan }}" class="text-right calculation-table fw-bold text-red">{{ __('Due Amount') }}</td>
                            <td class="text-right calculation-table fw-bold text-red">
                                {{ formatNumber($dueAmount, $currency) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="track-section">
            @if(count($order->shippingTracks(preference('product_label_wise_shipment_track'))) > 0)
                <h2 class="track-section-title">{{ __('Shipment Tracking Information') }}</h2>
                <div>
                    <table id="track-table">
                        <thead>
                            <tr>
                                @if(preference('product_label_wise_shipment_track'))
                                    <th>{{ __('Item') }}</th>
                                @endif
                                <th>{{ __('Provider') }}</th>
                                <th>{{ __('Tracking Number') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Tracking Url') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(preference('product_label_wise_shipment_track') == 0)
                                @if($order->shippingTrack)
                                    <tr>
                                        <td width="30%">{{ $order->shippingTrack->provider_name ?? 'No Info' }}</td>
                                        <td width="20%">{{ $order->shippingTrack->tracking_no ?? 'No Info' }}</td>
                                        <td width="25%">{{ $order->shippingTrack->order_shipped_date ?? 'No Info' }}</td>
                                        <td width="25%">
                                            <p>{{ $order->shippingTrack->tracking_link ?? 'No Info' }}</p>
                                        </td>
                                    </tr>
                                @endif
                            @else
                                @foreach ($orderDetails as $detail)
                                    @if($detail->shippingTrack)
                                        <tr>
                                            <td width="40%">{{ $detail->product_name ?? 'No Info' }}</td>
                                            <td width="20%">{{ $detail->shippingTrack->provider_name ?? 'No Info' }}</td>
                                            <td width="10%">{{ $detail->shippingTrack->tracking_no ?? 'No Info' }}</td>
                                            <td width="20%">{{ $detail->shippingTrack->order_shipped_date ?? 'No Info' }}</td>
                                            <td width="25%">
                                                <p>{{ $detail->shippingTrack->tracking_link ?? 'No Info' }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($invoiceSetting?->document?->delivery_details?->is_delivery_details)
            <div class="delivery-side">
                <p class="delivery-details">{{ empty($invoiceSetting?->document?->delivery_details?->delivery_details_labal) ? __('Delivery Details') : $invoiceSetting?->document?->delivery_details?->delivery_details_labal }}</p>
                <div>
                    @if ($invoiceSetting?->document?->delivery_details?->is_shopping_address)
                        @php
                            $shippingAddress = $order->getShippingAddress();
                            $billingAddress = $order->getBillingAddress();
                        @endphp
                        <div class="addresses">
                            <p class="shipping-address">{{ empty($invoiceSetting?->document?->delivery_details?->shopping_address_label) ? __('Shipping Address') : $invoiceSetting?->document?->delivery_details?->shopping_address_label }} :</p>
                            <p class="shipping-information">
                                {{ $shippingAddress->first_name . ' ' . $shippingAddress->last_name }}</p>
                            <p class="shipping-information">{{ __('Street Address') }}:
                                {{ $shippingAddress->address_1 }}{{ !empty($shippingAddress->address_2) ? ', ' . $shippingAddress->address_2 : '' }}
                            </p>
                            <p class="shipping-information">{{ __('City') }}: {{ ucfirst($shippingAddress->city) }}</p>
                            <p class="shipping-information">
                                {{ __('Postcode') . ' / ' . __('ZIP') }}:{{ $shippingAddress->zip }}
                            </p>
                            <p class="shipping-information">{{ __('Country') }}: {{ \Modules\GeoLocale\Entities\Country::getNameByCode($shippingAddress->country) }}
                            </p>
                            <p class="shipping-information">
                                {{ __('State') . ' / ' . __('Province') }}:{{ \Modules\GeoLocale\Entities\Division::getStateNameByCountryStateCode($shippingAddress->country, $shippingAddress->state) }}
                            </p>
                            @if (!empty($shippingAddress->phone))
                                <p class="shipping-information">{{ __('Phone') }}: {{ $shippingAddress->phone }}
                                </p>
                            @endif

                        </div>
                    @endif
                    <div class="payment">
                        @if ($invoiceSetting?->document?->delivery_details?->is_estimate_time_section)
                            <div>
                                <p class="shipping-address">{{ empty($invoiceSetting?->document?->delivery_details?->estimate_time_label) ? __('ESTIMATED DELIVERY TIME') : $invoiceSetting?->document?->delivery_details?->estimate_time_label }}</p>
                                <p class="shipping-information">{{ __('Office Days') }}</p>
                            </div>
                        @endif
                        @if ($invoiceSetting?->document?->delivery_details?->is_payment_section)
                            <div>
                                <p class="shipping-address">{{ empty($invoiceSetting?->document?->delivery_details?->payment_label) ? __('PAYMENT') : $invoiceSetting?->document?->delivery_details?->payment_label }}</p>
                                @if (!empty(optional($order->paymentMethod)->gateway))
                                    <p class="shipping-information">{{ optional($order->paymentMethod)->gateway }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="clear-both"></div>
                </div>

            </div>
        @endif

        <div>
            @if ($invoiceSetting?->document?->footer?->is_footer)
                @if ($invoiceSetting?->document?->footer?->is_main_footer)
                    <p class="keep-in-touch">{{ $invoiceSetting?->document?->footer?->main_footer?->label }}</p>
                    <p class="concern-queries"
                        style="color: {{ $invoiceSetting?->document?->footer?->main_footer?->text_color }}; text-align: {{ $invoiceSetting?->document->footer?->main_footer?->align }};">
                        {{ $invoiceSetting?->document?->footer?->main_footer?->content  }}</p>
                @endif                
                @if ($invoiceSetting?->document?->footer?->is_copy_right_footer)
                    <p 
                        class="copy-right"
                        style="color: {{ $invoiceSetting?->document?->footer?->copy_right_footer?->text_color }}; text-align: {{ $invoiceSetting?->document->footer?->copy_right_footer?->align }};">
                        {{ $invoiceSetting?->document?->footer?->copy_right_footer?->content  }}</p>
                @endif
            @endif
        </div>
    </div>
    @if ($type == 'print')
        <script src="{{ asset('public/dist/js/custom/site/order-invoice.min.js?v=4.2') }}"></script>
    @endif
</body>

</html>
