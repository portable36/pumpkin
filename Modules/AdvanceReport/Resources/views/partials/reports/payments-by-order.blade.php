{{-- Payments By Order Report --}}
<div class="table-wrapper" id="tableWrapper">
    <!-- Sticky header that appears when scrolling -->
    @php
        $headers = [
            ['column' => 'order_reference', 'label' => __('Order Reference'), 'align' => 'left'],
            ['column' => 'order_date', 'label' => __('Order Date'), 'align' => 'left'],
            ['column' => 'customer_name', 'label' => __('Customer Name'), 'align' => 'left'],
            ['column' => 'customer_email', 'label' => __('Email'), 'align' => 'left'],
            ['column' => 'order_total', 'label' => __('Order Total'), 'align' => 'right'],
            ['column' => 'order_paid', 'label' => __('Order Paid'), 'align' => 'right'],
            ['column' => 'payment_status', 'label' => __('Status'), 'align' => 'left'],
        ];
    @endphp
    <div class="sticky-header" id="stickyHeader">
        <div class="sticky-header-inner" id="stickyHeaderInner">
            <table class="table advance-report-table display nowrap comparison-table" id="advanceReportTable">
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            @include('advancereport::partials.reports._sortable-header', [
                                'column' => $header['column'],
                                'label' => $header['label'],
                                'align' => $header['align']
                            ])
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="table-scroll-container" id="tableContainer">
        <table class="table advance-report-table display nowrap comparison-table" id="mainTable" style="width:100%">
            <thead>
                <tr>
                    @foreach($headers as $header)
                        @include('advancereport::partials.reports._sortable-header', [
                            'column' => $header['column'],
                            'label' => $header['label'],
                            'align' => $header['align']
                        ])
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(isset($data['message']))
                    @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => $data['message']])
                @elseif(isset($data['paymentData']))
                    @if(count($data['paymentData']) > 0)
                        {{-- Summary Row - Always at top --}}
                        <tr class="comparison-row" data-row-type="summary">
                            <td class="font-weight-600">
                                <div>{{ __('Total') }}</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalAmount'] ?? 0 }}">
                                <div>{{ formatNumber($data['totalAmount'] ?? 0) }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalPaid'] ?? 0 }}">
                                <div>{{ formatNumber($data['totalPaid'] ?? 0) }}</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                        </tr>
                        {{-- Payment Data --}}
                        @foreach($data['paymentData'] as $payment)
                        <tr class="comparison-row">
                            <td class="font-weight-500" data-sort="{{ $payment['order_reference'] }}">
                                <div>{{ $payment['order_reference'] }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $payment['order_date'] }}">
                                <div>{{ formatDate($payment['order_date']) }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $payment['customer_name'] }}">
                                <div>{{ $payment['customer_name'] }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $payment['customer_email'] }}">
                                <div>{{ $payment['customer_email'] }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $payment['order_total'] }}">
                                <div>{{ formatNumber($payment['order_total']) }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $payment['order_paid'] }}">
                                <div>{{ formatNumber($payment['order_paid']) }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $payment['payment_status'] }}">
                                <div>{{ $payment['payment_status'] }}</div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('No payment data found.')])
                    @endif
                @else
                    @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('This report is not yet implemented.')])
                @endif
            </tbody>
        </table>
    </div>
</div>

@include('advancereport::partials.reports._pagination', ['paginator' => $data['paginator'] ?? null])

