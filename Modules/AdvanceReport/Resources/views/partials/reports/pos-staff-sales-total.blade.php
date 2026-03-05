{{-- POS Staff Sales Total Report --}}
<div class="table-wrapper" id="tableWrapper">
    <!-- Sticky header that appears when scrolling -->
    @php
        $headers = [
            ['column' => 'staff_name', 'label' => __('Staff Name'), 'align' => 'left'],
            ['column' => 'total_orders', 'label' => __('Total Orders'), 'align' => 'right'],
            ['column' => 'average_sales', 'label' => __('Average Sales'), 'align' => 'right'],
            ['column' => 'total_quantity', 'label' => __('Order Item Quantity'), 'align' => 'right'],
            ['column' => 'total_sales', 'label' => __('Total Sales'), 'align' => 'right'],
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
                @elseif(isset($data['staffData']))
                    @if(count($data['staffData']) > 0)
                        {{-- Summary Row - Always at top --}}
                        <tr class="comparison-row" data-row-type="summary">
                            <td class="font-weight-600">
                                <div>{{ __('Total') }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalOrders'] ?? 0 }}">
                                <div>{{ formatNumber($data['totalOrders'] ?? 0, ' ') }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['averageSales'] ?? 0 }}">
                                <div>{{ formatNumber($data['averageSales'] ?? 0) }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalQuantity'] ?? 0 }}">
                                <div>{{ formatNumber($data['totalQuantity'] ?? 0, ' ') }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalSales'] ?? 0 }}">
                                <div>{{ formatNumber($data['totalSales'] ?? 0) }}</div>
                            </td>
                        </tr>
                        {{-- Staff Data --}}
                        @foreach($data['staffData'] as $staff)
                        <tr class="comparison-row">
                            <td class="font-weight-500" data-sort="{{ $staff['staff_name'] }}">
                                <div>{{ $staff['staff_name'] }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $staff['total_orders'] }}">
                                <div>{{ formatNumber($staff['total_orders'], ' ') }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $staff['average_sales'] }}">
                                <div>{{ formatNumber($staff['average_sales']) }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $staff['total_quantity'] }}">
                                <div>{{ formatNumber($staff['total_quantity'], ' ') }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $staff['total_sales'] }}">
                                <div>{{ formatNumber($staff['total_sales']) }}</div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('No staff sales data found.')])
                    @endif
                @else
                    @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('This report is not yet implemented.')])
                @endif
            </tbody>
        </table>
    </div>
</div>

@include('advancereport::partials.reports._pagination', ['paginator' => $data['paginator'] ?? null])


