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
        $defaultCurrency = currency();
        $currency = $defaultCurrency?->name == 'USD' ? $defaultCurrency?->symbol : $defaultCurrency?->name . ' ';
    @endphp
    <div id="invoice-view-container">
        <div class="invoice-header">
            <div class="invoice-header-left">
                <span>
                    <img class="martvill-logo"
                        src="{{ domPdfImageSource($logo) }}">
                </span>
            </div>

            <div class="invoice-header-right">
                <div>
                    <h2 class="invoice-title">
                        {{ __('Purchase Invoice')  }}
                    </h2>
                    <p class="invoice-id"># {{ $purchase->reference }}</p>
                    <p class="invoice-date">
                        {{ __('Purchase Date: ') }} {{ $purchase->purchase_date }}
                    </p>

                    <p class="invoice-date">
                        {{ __('Payment Type: ') }} {{ $purchase->payment_type }}
                    </p>

                    <p class="invoice-date">
                        {{ __('Track code: ') }} {{ $purchase->tracking_number }}
                    </p>

                    <p class="invoice-status">
                        <span class="status-text {{ $purchase->payment_status == 'Paid' ? 'status-paid' : 'status-unpaid' }}">
                            {{ $purchase->payment_status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>


        <table class="billing-table">
            <tr>
                <!-- Bill From -->
                <td class="billing-cell">
                    <p class="billing-title">{{ __('Bill From') }}</p>
                    <p class="billing-name">{{ $purchase->supplier?->name }}</p>

                    @if ($purchase->supplier?->phone)
                        <p class="billing-text">{{ $purchase->supplier?->phone }}</p>
                    @endif

                    @if ($purchase->supplier?->email)
                        <p class="billing-text">{{ $purchase->supplier?->email }}</p>
                    @endif

                    <p class="billing-text">

                        @if ($purchase->supplier?->address)
                            {{ $purchase->supplier?->address }}
                        @endif

                        @if (!empty($purchase->supplier?->city))
                            {{ $purchase->supplier?->city }},
                        @endif

                        @if (!empty($purchase->supplier?->geoState))
                            {{ $purchase->supplier?->geoState?->name }},
                        @endif

                        @if (!empty($purchase->supplier?->geoCountry))
                            {{ $purchase->supplier?->geoCountry?->name }}
                        @endif

                        @if (!empty($purchase->supplier?->zip))
                            {{ $purchase->supplier?->zip }}
                        @endif
                    </p>
                    
                </td>

                <!-- Bill To -->
                <td class="billing-cell">
                    <p class="billing-title billing-to-offset">{{ __('Bill To') }}</p>
                    @if ($purchase->vendor?->name)
                        <p class="billing-name billing-to-offset">{{ $purchase->vendor?->name}}</p>
                    @endif

                    @if ($purchase->vendor?->phone)
                        <p class="billing-text billing-to-offset">{{ $purchase->vendor?->phone }}</p>
                    @endif

                    @if ($purchase->vendor?->email)
                        <p class="billing-text billing-to-offset">{{ $purchase->vendor?->email }}</p>
                    @endif

                    @if ($purchase->vendor?->address)
                        <p class="billing-text billing-to-offset">{{ $purchase->vendor?->address }}</p>
                    @endif
                </td>
            </tr>
        </table>


        <div class="invoice-wrapper">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>{{ __('Sl') }}</th>

                        <th>
                            {{ __('Product Name') }}
                        </th>
                        <th>
                            {{ __('Unit') }}
                        </th>
                        
                        <th class="text-center">
                            {{ __('Quantity') }}
                        </th>

                        <th class="text-right">
                            {{ __('Cost') }}
                        </th>

                        <th class="text-right">{{ __('Tax') }} %</th>
                        <th class="text-right">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->purchaseDetail as $detail)
                        
                        <tr>
                            <td class="td pdf-product-name-td text-center">
                                <p>{{ $loop->index + 1 }}</p>
                            </td>

                            <td class="td pdf-product-name-td">
                                <p>{{ $detail->product_display_name }}</p>
                            </td>
                            <td class="td pdf-product-name-td">
                                <p>{{ $detail->unit ?? defaultUnit()->abbr }}</p>
                            </td>
                           
                            <td class="td text-center pdf-product-name-td">
                                <p>{{ formatCurrencyAmount($detail->quantity) }}</p>
                            </td>

                            <td class="td text-center pdf-product-name-td">
                                <p>{{ formatNumber($detail->amount, $currency) }}</p>
                            </td>

                            <td class="td text-right pdf-product-name-td">
                                <p>{{ formatCurrencyAmount($detail->tax_charge) }}</p>
                            </td>
                            @php
                                $tax = (($detail->quantity * $detail->amount) * $detail->tax_charge)/100;
                            @endphp
                            <td class="td text-right pdf-product-name-td">
                                <p>{{ formatNumber(($detail->amount * $detail->quantity) + $tax, $currency) }}</p>
                            </td>
                        </tr>
                    @endforeach

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

                    <tr>
                        <td colspan="6" class="text-right calculation-table">{{ __('Taxes (Included)') }}</td>
                        <td class="text-right calculation-table">
                            {{ formatCurrencyAmount($purchase->tax_charge) }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" class="text-right calculation-table">{{ __('Sub Total') }}</td>
                        <td class="text-right calculation-table">
                            {{ formatCurrencyAmount($subTotal) }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" class="text-right calculation-table">{{ __('Shipping') }}</td>
                        <td class="text-right calculation-table">
                            {{ formatCurrencyAmount($purchase->shipping_charge) }}
                        </td>
                    </tr>

                    @if (isset($adjustments['name']))

                        @foreach($adjustments['name'] as $key => $adjust)

                            <tr>
                                <td colspan="6" class="text-right calculation-table">
                                    {{ $adjust }}
                                </td>
                                <td class="text-right calculation-table">
                                    {{ $adjustments['amount'][$key] }}
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <tr class="total-header">
                        <td colspan="6" class="text-right calculation-table total-amount">{{ __('Total') }}</td>
                        <td class="text-right calculation-table total-amount">
                            {{ formatNumber($purchase->total_amount, $currency) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($payments->count() > 0)
        <div class="track-section">
            <h2 class="track-section-title">{{ __('Payments') }}</h2>

            <div>
                <table id="track-table">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td width="20%">{{ formatDate($payment['date']) }}</td>
                                <td width="25%">{{ $payment['payment_method'] }}</td>
                                <td width="20%">{{ formatNumber($payment['amount'], $currency) }}</td>
                                <td width="35%">{{ $payment['description'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
    </div>
    @if ($type == 'print')
        <script src="{{ asset('public/dist/js/custom/order-invoice.min.js') }}"></script>
    @endif
</body>

</html>
