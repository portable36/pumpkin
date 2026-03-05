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
                                    <h5 class="card-title text-uppercase"> {{ __('Create Invoice') }}</h5>
                                    <div class="row">
                                        <div class="col-sm-12 col-md">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="card-subtitle text-muted">{{ __('Payment Status') }} :
                                                        <span
                                                            class="badge badge-mv-danger payment-status-badge">{{ __('Unpaid') }}</span>
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
                                                <div class="col-md-10 {{ isset($vendor) ? 'd-none' : '' }}">
                                                    <h6>{{ __('Vendor') }}:</h6>
                                                    <select class="form-control" name="vendor_id"
                                                        id="vendor_id">
                                                        @if (isset($vendor))
                                                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->id }}</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-md-10 {{ isset($vendor) ? '' : 'mt-2' }}">
                                                    <h6>{{ __('Customers') }}:</h6>

                                                    <select class="form-control select-user select2" name="user_id"
                                                        id="user_id">
                                                        <option>{{ __('Guest') }}</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-10 mt-2">
                                                    <h6>{{ __('Location') }}:</h6>

                                                    <select class="form-control select2" name="location_id"
                                                        id="location_id">
                                                        {{-- Data will be load from ajax --}}
                                                        @if (isset($location))
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
                                                        value='{{ date('Y-m-d') }}' type="text">
                                                </div>
                                                <input type="hidden" name="payment_method" id="payment_method"
                                                    value="">

                                                <div class="col-md-10 mt-2">
                                                    <h6>{{ __('Status') }}:</h6>
                                                    <select class="form-control order-status select2" name="status"
                                                        id="status">
                                                        @foreach ($orderStatus as $status)
                                                            @continue($status->payment_scenario == 'paid')
                                                            <option
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
                                                <a class="text-dark billing-address-edit d-none" href="javascript:void(0)" data-bs-toggle="modal"
                                                    data-bs-target="#update_billing_address">
                                                    <i class="feather icon-edit-1"></i>
                                                </a>
                                            </div>
                                            <address>
                                                <span class="billing-address-name">---------------</span><br>
                                                <span class="billing-address-phone">---------------</span><br>
                                                <span class="billing-address-email">--------------------</span><br>
                                                <div class="mb-1">-------------------------------------------</div>
                                                <span
                                                    class="billing-address-street">-----------------------------</span><br>
                                                <span class="billing-address-city">--------------</span>,
                                                <span class="billing-address-state">-------------</span>
                                                <br>
                                                <span class="billing-address-country">-------------</span>,
                                                <span class="billing-address-zip">---------------</span>
                                                <span class="billing-address-type-of-place"></span>
                                            </address>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark mb-3 mt-3 mt-md-0">
                                                <span class="pe-2">{{ __('Ship To') }}</span>
                                                <a class="text-dark shipping-address-edit d-none" href="javascript:void(0)"
                                                    id="shipping_address_edit" data-bs-toggle="modal"
                                                    data-bs-target="#update_shipping_address">
                                                    <i class="feather icon-edit-1"></i>
                                                </a>
                                            </div>
                                            <address>
                                                <span class="shipping-address-name">---------------</span><br>
                                                <span class="shipping-address-phone">---------------</span><br>
                                                <span class="shipping-address-email">--------------------</span><br>
                                                <div class="mb-1">-------------------------------------------</div>
                                                <span
                                                    class="shipping-address-street">-----------------------------</span><br>
                                                <span class="shipping-address-city">--------------</span>,
                                                <span class="shipping-address-state">-------------</span>
                                                <br>
                                                <span class="shipping-address-country">-----------</span>,
                                                <span class="shipping-address-zip">---------------</span>
                                                <span class="shipping-address-type-of-place"></span>
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
                                                            <th class="align-center">{{ __('Unit') }}</th>
                                                            <th class="align-center w-10">{{ __('Qty') }}</th>
                                                            <th class="align-center">{{ __('Cost') }}</th>
                                                            <th class="align-center w-5">{{ __('Tax') }}</th>
                                                            <th class="text-end w-5">{{ __('Total') }}</th>
                                                            <th class="text-end"><i class="feather icon-settings"></i>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="product-list-tbody">
                                                        <tr>
                                                            <td colspan="8" class="pt-5 no-border"></td>
                                                        </tr>

                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2" class="text-end fw-bold title">
                                                                {{ __('Sub Total') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price subtotal"
                                                                data-amount="0">{{ formatNumber(0.0) }}</td>
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
                                                            <td colspan="2"
                                                                class="text-end text-dark adjustment-fee price"
                                                                data-amount="0">{{ formatNumber(0.0) }}</td>
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
                                                                data-amount="0">{{ formatNumber(0.0) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold title tax-title">
                                                                {{ __('Tax') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price tax-amount"
                                                                data-amount="0">{{ formatNumber(0.0) }}</td>
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
                                                                data-amount="0">{{ formatNumber(0.0) }}</td>
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
                                                                data-amount="0">{{ formatNumber(0.0) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area border-bottom-0">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-dark title border-bottom-0">
                                                                {{ __('Total') }}:</td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-dark price border-bottom-0 pb-4 grand-total">
                                                                {{ formatNumber(0.0) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="form-group m-0 p-2">
                                                    <label for="customer_note"
                                                        class="control-label text-dark">{{ __('Customer Note') }}</label>
                                                    <div>
                                                        <textarea name="customer_note" class="form-control" rows="3"></textarea>
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
