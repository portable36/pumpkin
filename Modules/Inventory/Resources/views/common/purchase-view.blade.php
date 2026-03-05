<div class="row">
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
                                    <h5 class="card-title text-uppercase"> {{ __('View Purchase') }}</h5>
                                    <div class="row">
                                        <div class="col-sm-12 col-md">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="card-subtitle text-muted">{{ __('Payment Status') }} :
                                                        <span
                                                            class="badge {{ $purchase->payment_status == 'Paid' ? 'badge-mv-success' : 'badge-mv-danger' }} payment-status-badge">{{ $purchase->payment_status }}</span>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-header-right">

                                        @php
                                            $editRoute = 'purchase.edit';
                                            $pdfRoute = 'purchase.print';
                                            $paymentRoute = 'purchase.payment';
                                            if (isset($panel) && $panel == 'vendor') {
                                                $editRoute = 'vendor.purchase.edit';
                                                $pdfRoute = 'vendor.purchase.print';
                                                $paymentRoute = 'vendor.purchase.payment';
                                            }
                                        @endphp

                                        <a class="order-invoice-action"
                                            href="{{ route($editRoute, ['id' => $purchase->id]) }}"><i
                                                class="feather icon-edit"></i></a>

                                        <a class="order-invoice-action" target="_blank" href="{{ route($pdfRoute, ['id' => $purchase->id, 'type' => 'print' ]) }}"><i
                                                class="fas fa-file-pdf"></i></a>
                                        
                                        <a class="order-invoice-action {{ $purchase->total_amount - $purchase->paid_amount <= 0 ? 'disabled text-muted disabled-btn' : '' }}"
                                            href="javascript:void(0)" data-bs-toggle="modal"
                                            data-bs-target="{{ $purchase->total_amount - $purchase->paid_amount <= 0 ? '' : '#purchase_payment' }}">
                                            {{ __('Payment') }}
                                        </a>
                                    </div>
                                </div>

                                {{-- Invoice info --}}
                                <div class="card-block pb-5">
                                    <div class="row invoive-info">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark f-20 mb-3 mt-3 mt-md-0">
                                                #{{ $purchase->reference }}
                                            </div>
                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Purchase Date') }}:</span>
                                                {{ formatDate($purchase->purchase_date) }}
                                            </div>
                                            
                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Payment Type') }}:</span>
                                                {{ $purchase->payment_type }}
                                            </div>

                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Inventory') }}:</span>
                                                {{ $purchase->location?->name }}
                                            </div>

                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Track code') }}:</span>
                                                {{ $purchase->tracking_number }}
                                            </div>

                                            <div class="text-dark mb-1">
                                                <span class="fw-bold">{{ __('Status') }}:</span>
                                                {{ $purchase->status }}
                                            </div>

                                        </div>
                                        @unless(isset($panel) && $panel == 'vendor')
                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark mb-3 mt-3 mt-md-0">
                                                <span class="pe-2">{{ __('Bill To') }}</span>
                                            </div>
                                            <address>
                                                <span class="billing-address-name">{{ $purchase->vendor?->name }}</span><br>
                                                
                                                <span class="billing-address-phone">{{ $purchase->vendor?->phone }}</span><br>
                                                
                                                
                                                <span class="billing-address-email">{{ $purchase->vendor?->email }}</span><br>

                                                @if ($purchase->vendor?->address)
                                                    <span class="billing-address-street">{{ $purchase->vendor?->address }} </span>
                                                @endif
                                            </address>
                                        </div>
                                        @endunless
                                        <div class="col-md-3 col-sm-6">
                                            <div class="fw-bold text-dark mb-3 mt-3 mt-md-0">
                                                <span class="pe-2">{{ __('Bill Form') }}</span>
                                            </div>
                                            <address>
                                                <span
                                                    class="shipping-address-name">{{ $purchase->supplier?->name }}</span><br>
                                                @if ($purchase->supplier?->phone)
                                                    <span class="shipping-address-phone">{{ $purchase->supplier?->phone }}</span><br>
                                                @endif

                                                @if ($purchase->supplier?->email)
                                                    <span class="shipping-address-email">{{ $purchase->supplier?->email }}</span><br>
                                                @endif

                                                @if (!empty($purchase->supplier?->address))
                                                    <span
                                                        class="shipping-address-street">{{ $purchase->supplier?->address }}
                                                        </span><br>
                                                @endif

                                                @if (!empty($purchase->supplier?->city))
                                                    <span class="shipping-address-city">{{ $purchase->supplier?->city }}</span>,
                                                @endif

                                                @if (!empty($purchase->supplier?->geoState))
                                                    <span class="shipping-address-state">{{ $purchase->supplier?->geoState?->name }}</span>,
                                                @endif

                                                @if (!empty($purchase->supplier?->geoCountry))
                                                    <span
                                                        class="shipping-address-country">{{ $purchase->supplier?->geoCountry?->name }}</span>
                                                @endif

                                                @if (!empty($purchase->supplier?->zip))
                                                    <span
                                                        class="shipping-address-zip">{{ $purchase->supplier?->zip }}</span>
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
                                                            <th>{{ __('Products') }}</th>
                                                            <th class="text-center w-10">{{ __('Supplier SKU') }}</th>
                                                            <th class="itemQty text-center w-10">{{ __('Quantity') }}</th>
                                                            <th class="text-center w-10">{{ __('Unit') }}</th>
                                                            <th class="text-center w-10">{{ __('Cost') }}</th>
                                                            <th class="text-center w-10">{{ __('Tax') }} %</th>
                                                            <th class="text-center w-10">{{ __('Total') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="product-list-tbody">
                                                        
                                                        @foreach ($purchase->purchaseDetail as $detail)
                                                            
                                                            <tr class="product-list product-list-tr-view">
                                                                <td>
                                                                    <div class="text-start">
                                                                        <div class="text-dark">
                                                                            <span
                                                                                class="mt-1 d-block">{{ $detail->product_display_name }}</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ $detail->sku }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ formatCurrencyAmount($detail->quantity) }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ $detail->unit ?? defaultUnit()?->abbr }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ formatNumber($detail->amount) }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ formatCurrencyAmount($detail->tax_charge) }}
                                                                    </div>
                                                                </td>

                                                                @php
                                                                    $tax = (($detail->quantity * $detail->amount) * $detail->tax_charge) / 100;
                                                                @endphp
                                                                <td>
                                                                    <div class="text-center fw-bold">
                                                                        {{ formatNumber(($detail->amount * $detail->quantity) + $tax) }}
                                                                    </div>
                                                                </td
                                                            </tr>
                                                        @endforeach

                                                        <tr>
                                                            <td colspan="6" class="pt-5 no-border"></td>
                                                        </tr>

                                                        @php
                                                            $subTotal = $purchase->total_amount;
                                                            
                                                            if ($purchase->shipping_charge > 0) {
                                                                $subTotal -= $purchase->shipping_charge;
                                                            }
                                            
                                                            $adjustments = !empty($purchase->adjustment) ? json_decode($purchase->adjustment, true) : '';
                                                        
                                                            if (isset($adjustments['name'])) {
                                                                foreach ($adjustments['name'] as $key => $adjust) {
                                                                    $subTotal -= $adjustments['amount'][$key];
                                                                }
                                                            }
                                                            
                                                        @endphp

                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Taxes (Included)') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price subtotal"
                                                                data-amount="0">{{ formatCurrencyAmount($purchase->tax_charge) }}</td>
                                                        </tr>

                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Sub Total') }}</td>
                                                            <td colspan="2"
                                                                class="text-end text-dark price subtotal"
                                                                data-amount="0">{{ formatCurrencyAmount($subTotal) }}</td>
                                                        </tr>

                                                        
                                                        <tr class="total-summary-area">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="1" class="text-end fw-bold title">
                                                                {{ __('Shipping') }}
                                                            </td>
                                                            
                                                            <td colspan="2"
                                                                class="text-end text-dark adjustment-fee price"
                                                                data-amount="0">{{ formatCurrencyAmount($purchase->shipping_charge) }}</td>
                                                        </tr>

                                                        @if (isset($adjustments['name']))

                                                            @foreach($adjustments['name'] as $key => $adjust)

                                                                <tr class="total-summary-area">
                                                                    <td colspan="4" class="no-border"></td>
                                                                    <td colspan="1"
                                                                        class="text-end fw-bold title tax-title">
                                                                        {{ $adjust }}</td>
                                                                    <td colspan="2"
                                                                        class="text-end text-dark price tax-amount"
                                                                        data-amount="0">{{ $adjustments['amount'][$key] }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        
                                                        <tr class="total-summary-area border-bottom-0">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="1"
                                                                class="text-end fw-bold text-dark title border-bottom-0">
                                                                {{ __('Total') }}</td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-dark price border-bottom-0 grand-total">
                                                                {{ formatNumber($purchase->total_amount) }}</td>
                                                        </tr>
                                                        <tr class="total-summary-area border-bottom-0">
                                                            <td colspan="4" class="no-border"></td>
                                                            <td colspan="1"
                                                                class="text-end fw-bold text-dark title border-bottom-0">
                                                                {{ __('Due') }}</td>
                                                            <td colspan="2"
                                                                class="text-end fw-bold text-danger price border-bottom-0 due-amount">
                                                                {{ formatNumber($purchase->total_amount - $purchase->paid_amount) }}
                                                            </td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                </table>
                                                @if ($purchase->note)
                                                    <div class="form-group m-0 p-2">
                                                        <label for="customer_note"
                                                            class="control-label text-dark">{{ __('Note To Supplier') }}:</label>
                                                        <div>
                                                            {{ $purchase->note }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($payments->count() > 0)
                                    <div class="card-footer invoice-footer">
                                        <div class="table-responsive">
                                            <table class="table invoice-detail-table">
                                                <thead>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Payment Method') }}</th>
                                                    <th>{{ __('Amount') }}</th>
                                                    <th>{{ __('Note') }}</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($payments as $payment)
                                                        <tr>
                                                            <td>{{ formatDate($payment['date']) }}</td>
                                                            <td>{{ $payment['payment_method'] }}</td>
                                                            <td>{{ formatNumber($payment['amount']) }}</td>
                                                            <td>{{ $payment['description'] }}</td>
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

@include('inventory::common.modals.purchase-payment')
