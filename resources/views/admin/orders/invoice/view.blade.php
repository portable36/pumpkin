<div class="row mb-4">
    <div class="col-md-12">
        <div class="main-body">
            <div class="page-wrapper">
                <!-- [ Main Content ] start -->
                <div class="row">
                    <!-- [ Invoice ] start -->
                    <div class="container">
                        <div>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title text-uppercase"> {{ __('View Invoice') }}</h5>
                                    <div class="row">
                                        <div class="col-sm-12 col-md">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="card-subtitle text-muted">{{ __('Payment Status') }} :
                                                        <span
                                                            class="badge {{ $order->payment_status == 'Paid' ? 'badge-mv-success' : 'badge-mv-danger' }} payment-status-badge">{{ $order->payment_status }}</span>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-header-right">
                                        @php
                                            $editRoute = 'order.edit';
                                            $pdfRoute = 'invoice.print';
                                            if (isset($panel) && $panel == 'vendor') {
                                                $editRoute = 'vendorOrder.edit';
                                                $pdfRoute = 'vendorInvoice.print';
                                            }
                                        @endphp
                                        @if ($order->payment_status != 'Paid')
                                            <a class="order-invoice-action"
                                                href="{{ route($editRoute, ['id' => $order->id]) }}">
                                                <i class="feather icon-edit"></i></a>
                                        @endif
                                        <a class="order-invoice-action" target="_blank" href="{{ route($pdfRoute, ['id' => $order->id, 'type' => 'print' ]) }}"><i
                                                class="fas fa-file-pdf"></i></a>
                                        @php
                                            $hasCustomerOrUser = $order->customer_id || $order->user_id;
                                            $isVendorPanel = isset($panel) && $panel === 'vendor';
                                        @endphp

                                        @if ($hasCustomerOrUser || !$isVendorPanel)
                                            <div class="dropdown email">
                                                <div class="dd-button email">
                                                    <a href="javascript:void(0)" class="order-invoice-action ps-2 pe-4">
                                                        <i class="feather icon-mail"></i>
                                                    </a>
                                                </div>

                                                <ul class="dd-menu email d-none">
                                                    @if ($hasCustomerOrUser)
                                                        <li>
                                                            <a href="javascript:void(0)" id="send_mail_to_customer">
                                                                {{ __('Send Mail to Customer') }}
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @unless($isVendorPanel)
                                                        <li>
                                                            <a href="javascript:void(0)" id="send_mail_to_vendor">
                                                                {{ __('Send Mail to Vendor') }}
                                                            </a>
                                                        </li>
                                                    @endunless
                                                </ul>
                                            </div>
                                        @endif

                                        <a class="order-invoice-action {{ $order->total - $order->amount_received <= 0 ? 'disabled text-muted disabled-btn' : '' }}"
                                            href="javascript:void(0)" data-bs-toggle="modal"
                                            data-bs-target="{{ $order->total - $order->amount_received <= 0 ? '' : '#invoice_payment' }}">
                                            {{ __('Payment') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Invoice info --}}
                                <div class="card-block pb-5">
                                    <div class="row invoive-info">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark f-20 mb-3 mt-3 mt-md-0">
                                                #{{ $order->reference }}
                                            </div>
                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Invoice Date') }}:</span>
                                                {{ formatDate($order->order_date) }}
                                            </div>

                                            @if ($order->paymentMethod?->gateway)
                                                <div class="text-dark mb-1">
                                                    <span class="fw-bold">{{ __('Gateway') }}:</span>
                                                    {{ paymentRenamed($order->paymentMethod?->gateway) }}
                                                </div>
                                            @endif
                                            
                                            @unless(isset($panel) && $panel == 'vendor')
                                                <div class="text-dark mb-1">
                                                    <span class="fw-bold">{{ __('Vendor') }}:</span>
                                                    {{ $order->orderDetails->first()?->vendor?->name }}
                                                </div>
                                            @endunless

                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Customer') }}:</span>
                                                {{ $order->user?->name ?? $order->customer?->name ?? $order->customer?->phone ?? __('Guest') }}
                                            </div>
                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Inventory') }}:</span>
                                                {{ $location?->name }}
                                            </div>
                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Track code') }}:</span>
                                                {{ isset($order->getMeta()['track_code']) ? $order->getMeta()['track_code'] : __('Unavailable') }}
                                            </div>
                                            @if ($orderStatus)
                                                <div class="text-dark mb-1">
                                                    <span class="fw-bold">{{ __('Status') }}:</span>
                                                    {{ $orderStatus->name }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark mb-3 mt-3 mt-md-0">
                                                <span class="pe-2">{{ __('Bill To') }}</span>
                                            </div>
                                            <address>
                                                @if (!empty($billingAddress->first_name))
                                                    <span
                                                        class="billing-address-name">{{ $billingAddress->first_name . ' ' . $billingAddress->last_name }}</span><br>
                                                @else
                                                    <span class="billing-address-name">---------------</span><br>
                                                @endif
                                                @if (!empty($billingAddress->phone))
                                                    <span
                                                        class="billing-address-phone">{{ $billingAddress->phone }}</span><br>
                                                @else
                                                    <span class="billing-address-phone">---------------</span><br>
                                                @endif
                                                @if (!empty($billingAddress->email))
                                                    <span
                                                        class="billing-address-email">{{ $billingAddress->email }}</span><br>
                                                @else
                                                    <span class="billing-address-email">--------------------</span><br>
                                                @endif
                                                <div class="mb-1">-------------------------------------------</div>
                                                @if (!empty($billingAddress->address_1))
                                                    <span
                                                        class="billing-address-street">{{ $billingAddress->address_1 }}
                                                        {{ !empty($billingAddress->address_2) ? ', ' . $billingAddress->address_2 : '' }}</span><br>
                                                @else
                                                    <span class="billing-address-street">-------------</span><br>
                                                @endif
                                                @if (!empty($billingAddress->city))
                                                    <span
                                                        class="billing-address-city">{{ $billingAddress->city }}</span>,
                                                @else
                                                    <span class="billing-address-city">--------------</span>,
                                                @endif
                                                @if (!empty($billingAddress->state))
                                                    <span
                                                        class="billing-address-state">{{ $billingAddress->state }}</span>
                                                @else
                                                    <span class="billing-address-state">-------------</span>
                                                @endif
                                                <br>
                                                @if (!empty($billingAddress->country))
                                                    <span
                                                        class="billing-address-country">{{ $billingAddress->country }}</span>,
                                                @else
                                                    <span class="billing-address-country">----------</span>,
                                                @endif
                                                @if (!empty($billingAddress->zip))
                                                    <span class="billing-address-zip">{{ $billingAddress->zip }}</span>
                                                @else
                                                    <span class="billing-address-zip">---------------</span>
                                                @endif
                                                @if (!empty($billingAddress->type_of_place))
                                                    <span
                                                        class="billing-address-type-of-place">({{ ucfirst($billingAddress->type_of_place) }})</span>
                                                @endif
                                            </address>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark mb-3 mt-3 mt-md-0">
                                                <span class="pe-2">{{ __('Ship To') }}</span>
                                            </div>
                                            <address>
                                                @if (!empty($shippingAddress->first_name))
                                                    <span
                                                        class="shipping-address-name">{{ $shippingAddress->first_name . ' ' . $shippingAddress->last_name }}</span><br>
                                                @else
                                                    <span class="shipping-address-name">---------------</span><br>
                                                @endif

                                                @if (!empty($shippingAddress->phone))
                                                    <span
                                                        class="shipping-address-phone">{{ $shippingAddress->phone }}</span><br>
                                                @else
                                                    <span class="shipping-address-phone">---------------</span><br>
                                                @endif

                                                @if (!empty($shippingAddress->email))
                                                    <span
                                                        class="shipping-address-email">{{ $shippingAddress->email }}</span><br>
                                                @else
                                                    <span class="shipping-address-email">--------------------</span><br>
                                                @endif
                                                <div class="mb-1">-------------------------------------------</div>
                                                @if (!empty($shippingAddress->address_1))
                                                    <span
                                                        class="shipping-address-street">{{ $shippingAddress->address_1 }}
                                                        {{ !empty($shippingAddress->address_2) ? ', ' . $shippingAddress->address_2 : '' }}</span><br>
                                                @else
                                                    <span class="shipping-address-street">-------------</span><br>
                                                @endif
                                                @if (!empty($shippingAddress->city))
                                                    <span
                                                        class="shipping-address-city">{{ $shippingAddress->city }}</span>,
                                                @else
                                                    <span class="shipping-address-city">--------------</span>,
                                                @endif
                                                @if (!empty($shippingAddress->state))
                                                    <span
                                                        class="shipping-address-state">{{ $shippingAddress->state }}</span>
                                                @else
                                                    <span class="shipping-address-state">-------------</span>
                                                @endif
                                                <br>
                                                @if (!empty($shippingAddress->country))
                                                    <span
                                                        class="shipping-address-country">{{ $shippingAddress->country }}</span>,
                                                @else
                                                    <span class="shipping-address-country">----------</span>,
                                                @endif
                                                @if (!empty($shippingAddress->zip))
                                                    <span
                                                        class="shipping-address-zip">{{ $shippingAddress->zip }}</span>
                                                @else
                                                    <span class="shipping-address-zip">---------------</span>
                                                @endif
                                                @if (!empty($shippingAddress->type_of_place))
                                                    <span
                                                        class="shipping-address-type-of-place">({{ ucfirst($shippingAddress->type_of_place) }})</span>
                                                @endif
                                            </address>
                                        </div>
                                    </div>
                                </div>
                                {{-- Product part --}}
                                <div class="card-block calculations_div border-top pt-5" id="calculations_div">
                                    {{-- Product list --}}
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table invoice-detail-table">
                                                    <thead class="product-list-thead">
                                                        <tr class="thead-default">
                                                            <th class="w-5"><i
                                                                    class="feather icon-camera ms-1"></i></th>
                                                            <th>{{ __('Products') }}</th>
                                                            <th class="align-center">{{ __('Unit') }}</th>
                                                            <th class="align-center w-10">{{ __('Qty') }}</th>
                                                            <th class="align-center">{{ __('Cost') }}</th>
                                                            <th class="text-end w-5">{{ __('Total') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="product-list-tbody">
                                                        @php
                                                            $subTotal = 0;
                                                        @endphp
                                                        @foreach ($order->orderDetails as $detail)
                                                            @php
                                                                $subTotal += $detail->price * $detail->quantity;
                                                            @endphp
                                                            <tr class="product-list product-list-tr-view">
                                                                <td>
                                                                    @php
                                                                        if (is_null($detail->parent_id)) {
                                                                            $productImage = $detail->product->getFeaturedImage();
                                                                        } else {
                                                                            $productImage = $detail->product->getImages(
                                                                                true,
                                                                                'small',
                                                                            );

                                                                            if (is_array($productImage)) {
                                                                                $productImage = $productImage[0];
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <img src="{{ $productImage }}"
                                                                        class="product-img"
                                                                        alt="{{ __('Product Image') }}">
                                                                </td>
                                                                <td>
                                                                    <div class="text-start">
                                                                        <div class="text-dark">
                                                                            <span
                                                                                class="mt-1 d-block">{{ $detail->product_name }}</span>
                                                                            <p class="text-muted">
                                                                                {{ $detail->description }}</p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ $detail->unit ?? $unit->abbr }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ rtrim(rtrim(number_format((float) $detail->quantity, 4, '.', ''), '0'), '.') }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ formatNumber($detail->price) }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-end fw-bold">
                                                                        {{ formatNumber($detail->price * $detail->quantity) }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        <tr>
                                                            <td colspan="6" class="pt-5 no-border"></td>
                                                        </tr>

                                                        <tr class="total-summary-area">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Sub Total') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price subtotal"
                                                                data-amount="0">{{ formatNumber($subTotal) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Fee') }}
                                                            </td>
                                                            @php
                                                                $feeArray = json_decode($order->fee, true);
                                                                $totalFee = 0;
                                                                foreach ($feeArray ?? [] as $item) {
                                                                    $totalFee += (float) $item['amount'];
                                                                }
                                                            @endphp
                                                            <td colspan="2"
                                                                class="text-end text-dark adjustment-fee price"
                                                                data-amount="0">{{ formatNumber($totalFee) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Shipping') }}
                                                            </td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price shipping-fee"
                                                                data-amount="0">
                                                                {{ formatNumber($order->shipping_charge) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1"
                                                                class="text-end fw-bold title tax-title">
                                                                {{ __('Tax') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price tax-amount"
                                                                data-amount="0">{{ formatNumber($order->tax_charge) }}
                                                            </td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Coupon Offer') }}
                                                            </td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price coupon-amount"
                                                                data-amount="0">
                                                                {{ formatNumber(isset($order->couponRedeems) && $order->couponRedeems->sum('discount_amount') > 0 && isActive('Coupon') ? $order->couponRedeems->sum('discount_amount') : 0) }}
                                                            </td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Discount') }}
                                                            </td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price discount-amount"
                                                                data-amount="0">{{ formatNumber($order->other_discount_amount) }}
                                                            </td>
                                                        </tr>
                                                        <tr class="total-summary-area border-bottom-0">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1"
                                                                class="text-end fw-bold text-dark title border-bottom-0">
                                                                {{ __('Total') }}</td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-dark price border-bottom-0 grand-total">
                                                                {{ formatNumber($order->total) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area border-bottom-0">
                                                            <td colspan="3" class="no-border"></td>
                                                            <td colspan="1"
                                                                class="text-end fw-bold text-dark title border-bottom-0">
                                                                {{ __('Due') }}</td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-red price border-bottom-0 pb-4 grand-total">
                                                                {{ $order->amount_received > $order->total ? formatNumber(0) :  formatNumber($order->total - $order->amount_received) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @if ($order->customer_note)
                                                    <div class="form-group m-0 p-2">
                                                        <label for="customer_note"
                                                            class="control-label text-dark">{{ __('Customer Note') }}:</label>
                                                        <div>
                                                            {{ $order->customer_note }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($transactions->count() > 0)
                                    <div class="card-footer invoice-footer">
                                        <div class="table-responsive">
                                            <table class="table invoice-detail-table">
                                                <thead>
                                                    <th>{{ __('Transaction ID') }}</th>
                                                    <th>{{ __('Payment Method') }}</th>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transactions as $transaction)
                                                        @continue(!$transaction['amount'])
                                                        <tr>
                                                            <td>{{ $transaction['transaction_id'] }}</td>
                                                            <td>{{ $transaction['payment_method'] }}</td>
                                                            <td>{{ formatDate($transaction['date']) }}</td>
                                                            <td>{{ formatNumber($transaction['amount']) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
