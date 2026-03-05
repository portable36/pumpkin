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
                                    <h5 class="card-title text-uppercase"> {{ __('Edit Invoice') }}</h5>
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
                                </div>
                                {{-- Invoice info --}}
                                <div class="card-block pb-5">
                                    <input type="hidden" name="random_id" id="random_id" value="{{ Str::random(10) }}">
                                    <div class="row invoive-info">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="row">
                                                <div class="col-md-10 {{ isset($panel) && $panel == 'vendor' ? 'd-none' : '' }}">
                                                    <h6>{{ __('Vendor') }}:</h6>

                                                    <select class="form-control" name="vendor_id"
                                                        id="vendor_id">
                                                        @if ($vendor)
                                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-md-10 {{ isset($panel) && $panel == 'vendor' ? '' : 'mt-2' }}">
                                                    <h6>{{ __('Customers') }}:</h6>

                                                    <select class="form-control select-user select2" name="user_id"
                                                        id="user_id">
                                                        @if ($order->customer?->id)
                                                            <option value="{{ $order->customer?->id }}">
                                                                {{ $order->customer?->phone ? $order->customer?->name . ' (' . $order->customer?->phone . ')' : $order->customer?->name }}</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-md-10 mt-2">
                                                    <h6>{{ __('Location') }}:</h6>

                                                    <select class="form-control select2" name="location_id"
                                                        id="location_id">
                                                        {{-- Data will be load from ajax --}}
                                                        @if ($location)
                                                            <option value="{{ $location->id }}">{{ $location->name }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <div class="row mt-2 mt-sm-0">
                                                <div class="col-md-10">
                                                    <h6>{{ __('Order Date') }}:</h6>
                                                    <input class="form-control inputFieldDesign" id="order_date"
                                                        value='{{ $order->order_date }}' type="text">
                                                </div>
                                                <input type="hidden" name="payment_method" id="payment_method"
                                                    value="">

                                                <div class="col-md-10 mt-2">
                                                    <h6>{{ __('Status') }}:</h6>
                                                    @php
                                                        $isCompleted =
                                                            $order->order_status_id ==
                                                            $orderStatus->where('slug', 'completed')->first()?->id;
                                                    @endphp
                                                    <select class="form-control order-status select2" name="status"
                                                        id="status" {{ $isCompleted ? 'disabled' : '' }}>
                                                        @foreach ($orderStatus as $status)
                                                            @unless ($status->payment_scenario == 'unpaid' || ($status->slug == 'completed' && $order->payment_status == 'Paid'))
                                                                @continue
                                                            @endunless
                                                            <option @selected($order->order_status_id == $status->id)
                                                                data-payment_scenario="{{ $status->payment_scenario }}"
                                                                value="{{ $status->id }}">{{ $status->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark mb-3 mt-3 mt-md-0">
                                                <span class="pe-2">{{ __('Bill To') }}</span>
                                                <a class="text-dark billing-address-edit" href="javascript:void(0)" data-bs-toggle="modal"
                                                    data-bs-target="#update_billing_address">
                                                    <i class="feather icon-edit-1"></i>
                                                </a>
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
                                                @if (!empty($billingAddress->state_name))
                                                    <span
                                                        class="billing-address-state">{{ $billingAddress->state_name }}</span>
                                                @else
                                                    <span class="billing-address-state">-------------</span>
                                                @endif
                                                <br>
                                                @if (!empty($billingAddress->country_name))
                                                    <span
                                                        class="billing-address-country">{{ $billingAddress->country_name }}</span>,
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
                                                <a class="text-dark shipping-address-edit" href="javascript:void(0)"
                                                    id="shipping_address_edit" data-bs-toggle="modal"
                                                    data-bs-target="#update_shipping_address">
                                                    <i class="feather icon-edit-1"></i>
                                                </a>
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
                                                @if (!empty($shippingAddress->state_name))
                                                    <span
                                                        class="shipping-address-state">{{ $shippingAddress->state_name }}</span>
                                                @else
                                                    <span class="shipping-address-state">-------------</span>
                                                @endif
                                                <br>
                                                @if (!empty($shippingAddress->country_name))
                                                    <span
                                                        class="shipping-address-country">{{ $shippingAddress->country_name }}</span>,
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
                                    {{-- Search product --}}
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group product-search-parent">
                                                <input type="text" class="search-box" id="productSearch"
                                                    placeholder="{{ __('Search Product') }}">
                                                <span class="search-error text-danger d-none"></span>
                                                <div class="product-dropdown" data-load="false" id="productList">
                                                    {{-- Data will load from ajax --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                                                            <th class="align-center w-5">{{ __('Unit') }}</th>
                                                            <th class="align-center w-10">{{ __('Qty') }}</th>
                                                            <th class="align-center">{{ __('Cost') }}</th>
                                                            <th class="align-center w-5">{{ __('Tax') }}</th>
                                                            <th class="text-end w-5">{{ __('Total') }}</th>
                                                            <th class="text-end"><i class="feather icon-settings"></i>
                                                            </th>
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
                                                            <tr class="product-list product-list-tr">
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

                                                                    $productName = $detail->product_name;
                                                                    $payloads = json_decode($detail->payloads, true);

                                                                    $variationString = '';
                                                                    if (is_array($payloads) && !empty($payloads)) {
                                                                        $variationParts = array_map(
                                                                            fn($k) => "$k: {$payloads[$k]}",
                                                                            array_keys($payloads),
                                                                        );
                                                                        $variationString = implode(
                                                                            ', ',
                                                                            $variationParts,
                                                                        );

                                                                        foreach ($payloads as $value) {
                                                                            $productName = preg_replace(
                                                                                '/,?\s*' .
                                                                                    preg_quote($value, '/') .
                                                                                    '\b/',
                                                                                '',
                                                                                $productName,
                                                                            );
                                                                        }
                                                                        $productName = trim(rtrim($productName, ', '));
                                                                    }
                                                                @endphp

                                                                <td>
                                                                    <input type="hidden" name="product[id][]"
                                                                        class="product-id"
                                                                        value="{{ $detail->product_id }}">
                                                                    <input type="hidden"
                                                                        name="product[variation_meta][]"
                                                                        class="product-tax-class"
                                                                        value="{{ !empty($variationString) ? '(' . $variationString . ')' : '' }}">
                                                                    <img src="{{ $productImage }}"
                                                                        class="product-img"
                                                                        alt="{{ __('Product Image') }}">
                                                                </td>
                                                                <td>
                                                                    <div class="text-start">
                                                                        <div class="product-title">
                                                                            <span>{{ $productName }}</span>
                                                                            <span
                                                                                class="product-variation d-inline-block me-2">{{ !empty($variationString) ? ' (' . $variationString . ')' : '' }}</span>
                                                                            <textarea name="product[description][]"
                                                                                placeholder="{{ __('Add product IMEI, Serial number or other information here.') }}"
                                                                                class="form-control mt-2 product-description-textarea">{{ $detail->description }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex justify-content-between justify-content-md-end">
                                                                        <span
                                                                            class="d-md-none">{{ __('Unit') }}</span>
                                                                        <span class="text-end text-md-center d-inline d-md-block w-100">{{ $detail->unit ?? $unit->abbr }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        <label
                                                                            class="d-md-none">{{ __('Quantity') }}</label>
                                                                        @php
                                                                            $stockQuantity = $detail->product->getStockQuantity();
                                                                        @endphp
                                                                        <input type="text"
                                                                            name="product[quantity][]" min="1"
                                                                            max="{{ $stockQuantity != 'null' && $stockQuantity != 0 ? $stockQuantity : 1000 }}"
                                                                            class="form-control product-qty positive-float-number"
                                                                            value="{{ rtrim(rtrim(number_format($detail->quantity, 8, '.', ''), '0'), '.') }}">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        <label
                                                                            class="d-md-none">{{ __('Cost') }}</label>
                                                                        <input type="text" name="product[price][]"
                                                                            class="form-control positive-float-number product-cost"
                                                                            value="{{ rtrim(rtrim(number_format($detail->price, 8, '.', ''), '0'), '.') }}">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        <label
                                                                            class="d-md-none">{{ __('Tax') }}</label>
                                                                        <select name="product_tax[]"
                                                                            class="form-control select2 tax-select-box"
                                                                            disabled>
                                                                            <option value="">{{ __('No Tax') }}
                                                                            </option>
                                                                            @foreach ($taxes as $tax)
                                                                                <option
                                                                                    {{ $detail->product?->getTaxClass() == $tax['slug'] ? 'selected' : '' }}
                                                                                    value="{{ $tax['slug'] }}">
                                                                                    {{ $tax['name'] }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div
                                                                        class="d-flex justify-content-between justify-content-md-end">
                                                                        <span
                                                                            class="d-md-none">{{ __('Total Price') }}</span>
                                                                        <span
                                                                            class="product-total-price">{{ formatNumber($detail->price * $detail->quantity) }}</span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end">
                                                                    <div class="product-list-delete">
                                                                        <i
                                                                            class="feather icon-trash cursor-pointer"></i>
                                                                        <span
                                                                            class="d-md-none">{{ __('Delete') }}</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="8" class="pt-5 no-border"></td>
                                                        </tr>

                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2" class="text-end fw-bold title">
                                                                {{ __('Sub Total') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price subtotal"
                                                                data-amount="0">{{ formatNumber($subTotal) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2" class="text-end fw-bold title">
                                                                {{ __('Fee') }}
                                                                <a class="text-dark ms-2" href="javascript:void(0)"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#update_fee">
                                                                    <i class="feather icon-edit-1"></i>
                                                                </a>
                                                            </td>
                                                            @php
                                                                $data = json_decode($order->fee, true);
                                                                $total = 0;

                                                                if (is_array($data)) {
                                                                    foreach ($data as $item) {
                                                                        $total += (float) $item['amount'];
                                                                    }
                                                                }
                                                            @endphp
                                                            <td colspan="2"
                                                                class="text-end text-dark adjustment-fee price"
                                                                data-amount="0">{{ formatNumber($total) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2" class="text-end fw-bold title">
                                                                {{ __('Shipping') }}
                                                                <a class="text-dark ms-2" href="javascript:void(0)"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#update_shipping">
                                                                    <i class="feather icon-edit-1"></i>
                                                                </a>
                                                            </td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price shipping-fee"
                                                                data-amount="{{ $order->shipping_charge }}">
                                                                {{ formatNumber($order->shipping_charge) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold title tax-title">
                                                                {{ __('Tax') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price tax-amount"
                                                                data-amount="{{ $order->tax_charge }}">
                                                                {{ formatNumber($order->tax_charge) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2" class="text-end fw-bold title">
                                                                {{ __('Coupon Offer') }}
                                                                <a class="text-dark ms-2" href="javascript:void(0)"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#update_coupon">
                                                                    <i class="feather icon-edit-1"></i>
                                                                </a>
                                                            </td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price coupon-amount"
                                                                data-amount="{{ isset($order->couponRedeems) && $order->couponRedeems->sum('discount_amount') > 0 ? $order->couponRedeems->sum('discount_amount') : 0 }}">
                                                                {{ formatNumber(isset($order->couponRedeems) && $order->couponRedeems->sum('discount_amount') > 0 ? $order->couponRedeems->sum('discount_amount') : 0) }}
                                                            </td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2" class="text-end fw-bold title">
                                                                {{ __('Discount') }}
                                                                <a class="text-dark ms-2" href="javascript:void(0)"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#update_discount">
                                                                    <i class="feather icon-edit-1"></i>
                                                                </a>
                                                            </td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price discount-amount"
                                                                data-amount="{{ $order->other_discount_amount }}">
                                                                {{ formatNumber($order->other_discount_amount) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area border-bottom-0">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-dark title border-bottom-0">
                                                                {{ __('Total') }}:</td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-dark price border-bottom-0 pb-4 grand-total">
                                                                {{ formatNumber($order->total) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="form-group m-0 p-2">
                                                    <label for="customer_note"
                                                        class="control-label text-dark">{{ __('Customer Note') }}</label>
                                                    <div>
                                                        <textarea name="customer_note" class="form-control" rows="3">{{ $order->customer_note }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer invoice-footer">
                                    <button type="button"
                                        class="btn btn-sm btn-primary float-right save-invoice">{{ __('Save Invoice') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
