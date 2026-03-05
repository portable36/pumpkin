{{-- Sales By Customer Name Report --}}
<div class="table-wrapper" id="tableWrapper">
    <!-- Sticky header that appears when scrolling -->
    @php
        $headers = [
            ['column' => 'customer_name', 'label' => __('Customer Name'), 'align' => 'left'],
            ['column' => 'customer_email', 'label' => __('Email'), 'align' => 'left'],
            ['column' => 'vendor_name', 'label' => __('Vendor'), 'align' => 'left'],
            ['column' => 'total_quantity', 'label' => __('Total Quantity'), 'align' => 'right'],
            ['column' => 'order_count', 'label' => __('Number of Orders'), 'align' => 'right'],
            ['column' => 'total_sales', 'label' => __('Total Sales'), 'align' => 'right'],
            ['column' => 'total_paid', 'label' => __('Total Paid'), 'align' => 'right'],
            ['column' => 'avg_order_value', 'label' => __('Average Order Value'), 'align' => 'right'],
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
                @elseif(isset($data['customerData']))
                    @if(count($data['customerData']) > 0)
                        {{-- Summary Row - Always at top --}}
                        <tr class="comparison-row" data-row-type="summary">
                            <td class="font-weight-600">
                                <div>{{ __('Total') }}</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>{{ __('All Customers') }}</div>
                            </td>
                            <td class="font-weight-400 first-row">
                                <div>{{ __('All Vendors') }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalQuantity'] }}">
                                <div>{{ formatNumber($data['totalQuantity'], ' ') }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalOrders'] }}">
                                <div>{{ $data['totalOrders'] }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalSales'] }}">
                                <div>{{ formatNumber($data['totalSales']) }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalPaid'] ?? 0 }}">
                                <div>{{ formatNumber($data['totalPaid'] ?? 0) }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalOrders'] > 0 ? ($data['totalSales'] / $data['totalOrders']) : 0 }}">
                                <div>{{ formatNumber($data['totalOrders'] > 0 ? ($data['totalSales'] / $data['totalOrders']) : 0) }}</div>
                            </td>
                        </tr>
                        {{-- Customer Sales Data --}}
                        @foreach($data['customerData'] as $customer)
                        <tr class="comparison-row">
                            <td class="font-weight-500" data-sort="{{ $customer['customer_name'] }}">
                                <div>{{ $customer['customer_name'] }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $customer['customer_email'] }}">
                                <div>{{ $customer['customer_email'] }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $customer['vendor_name'] }}">
                                <div>{{ $customer['vendor_name'] }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $customer['total_quantity'] }}">
                                <div>{{ formatNumber($customer['total_quantity'], ' ') }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $customer['order_count'] }}">
                                <div>{{ $customer['order_count'] }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $customer['total_sales'] }}">
                                <div>{{ formatNumber($customer['total_sales']) }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $customer['total_paid'] ?? 0 }}">
                                <div>{{ formatNumber($customer['total_paid'] ?? 0) }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $customer['avg_order_value'] }}">
                                <div>{{ formatNumber($customer['avg_order_value']) }}</div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        @include('advancereport::partials.reports._empty-row', [
                            'colspan' => count($headers),
                            'message' => __('No sales data found.')
                        ])
                    @endif
                @else
                    @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('This report is not yet implemented.')])
                @endif
            </tbody>
        </table>
    </div>
</div>

@include('advancereport::partials.reports._pagination', ['paginator' => $data['paginator'] ?? null])
