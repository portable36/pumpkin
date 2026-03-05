{{-- Total Sales By Order Report --}}
<div class="table-wrapper" id="tableWrapper">
    <!-- Sticky header that appears when scrolling -->
    @php
        $headers = [
            ['column' => 'order_date', 'label' => __('Order Date'), 'align' => 'left'],
            ['column' => 'order_reference', 'label' => __('Reference'), 'align' => 'left'],
            ['column' => 'customer_name', 'label' => __('Customer'), 'align' => 'left'],
            ['column' => 'total_quantity', 'label' => __('Total Quantity'), 'align' => 'right'],
            ['column' => 'order_total', 'label' => __('Total'), 'align' => 'right'],
            ['column' => 'order_paid', 'label' => __('Paid'), 'align' => 'right'],
            ['column' => 'channel', 'label' => __('Channel'), 'align' => 'left'],
            ['column' => 'payment_status', 'label' => __('Payment Status'), 'align' => 'left'],
            ['column' => 'order_status', 'label' => __('Order Status'), 'align' => 'left'],
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
                @elseif(isset($data['orderData']))
                    @if(count($data['orderData']) > 0)
                        {{-- Summary Row - Always at top --}}
                        <tr class="comparison-row" data-row-type="summary">
                            <td class="font-weight-600">
                                <div>—</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalQuantity'] ?? 0 }}">
                                <div>{{ $data['totalQuantity'] ?? 0 }}</div>
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
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>—</div>
                            </td>
                        </tr>
                        {{-- Order Data --}}
                        @foreach($data['orderData'] as $order)
                        <tr class="comparison-row">
                            <td class="font-weight-400" data-sort="{{ $order['order_date'] }}">
                                <div>{{ timeZoneFormatDate($order['order_date']) }}</div>
                            </td>
                            <td class="font-weight-500" data-sort="{{ $order['order_reference'] }}">
                                <div>{{ $order['order_reference'] }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $order['customer_name'] }}">
                                <div>{{ $order['customer_name'] }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $order['total_quantity'] }}">
                                <div>{{ formatNumber($order['total_quantity'], ' ') }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $order['order_total'] }}">
                                <div>{{ formatNumber($order['order_total']) }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $order['order_paid'] }}">
                                <div>{{ formatNumber($order['order_paid']) }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $order['channel'] }}">
                                <div>{{ $order['channel'] }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $order['payment_status'] }}">
                                <div>{{ $order['payment_status'] }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $order['order_status'] }}">
                                <div>{{ $order['order_status'] }}</div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('No order data found.')])
                    @endif
                @else
                    @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('This report is not yet implemented.')])
                @endif
            </tbody>
        </table>
    </div>
</div>

@include('advancereport::partials.reports._pagination', ['paginator' => $data['paginator'] ?? null])


