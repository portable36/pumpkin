{{-- Total Sales By Product Report --}}
<div class="table-wrapper" id="tableWrapper">
    <!-- Sticky header that appears when scrolling -->
    @php
        // Define headers once to avoid repetition
        $headers = [
            ['column' => 'product_name', 'label' => __('Product'), 'align' => 'left'],
            ['column' => 'vendor_name', 'label' => __('Vendor'), 'align' => 'left'],
            ['column' => 'quantity_sold', 'label' => __('Quantity Sold'), 'align' => 'right'],
            ['column' => 'order_count', 'label' => __('Number of Orders'), 'align' => 'right'],
            ['column' => 'total_sales', 'label' => __('Total Sales'), 'align' => 'right'],
            ['column' => 'total_tax', 'label' => __('Tax'), 'align' => 'right'],
            ['column' => 'avg_unit_price', 'label' => __('Average Sale Price'), 'align' => 'right'],
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
                @elseif(isset($data['productData']))
                    @if(count($data['productData']) > 0)
                        {{-- Summary Row - Always at top --}}
                        <tr class="comparison-row" data-row-type="summary">
                            <td class="font-weight-600">
                                <div>{{ __('Total') }}</div>
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
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalTax'] ?? 0 }}">
                                <div>{{ formatNumber($data['totalTax'] ?? 0) }}</div>
                            </td>
                            <td class="text-right font-weight-600 first-row" data-sort="{{ $data['totalQuantity'] > 0 ? ($data['totalSales'] / $data['totalQuantity']) : 0 }}">
                                <div>{{ formatNumber($data['totalQuantity'] > 0 ? ($data['totalSales'] / $data['totalQuantity']) : 0) }}</div>
                            </td>
                        </tr>
                        {{-- Product Sales Data --}}
                        @foreach($data['productData'] as $product)
                        <tr class="comparison-row">
                            <td class="font-weight-500" data-sort="{{ $product['product_name'] }}">
                                <div>{{ \Illuminate\Support\Str::limit($product['product_name'], 80, '...') }}</div>
                            </td>
                            <td class="font-weight-400" data-sort="{{ $product['vendor_name'] }}">
                                <div>{{ $product['vendor_name'] }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $product['quantity_sold'] }}">
                                <div>{{ formatNumber($product['quantity_sold'], ' ') }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $product['order_count'] }}">
                                <div>{{ $product['order_count'] }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $product['total_sales'] }}">
                                <div>{{ formatNumber($product['total_sales']) }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $product['total_tax'] ?? 0 }}">
                                <div>{{ formatNumber($product['total_tax'] ?? 0) }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $product['avg_unit_price'] }}">
                                <div>{{ formatNumber($product['avg_unit_price']) }}</div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('No sales data found.')])
                    @endif
                @else
                    @include('advancereport::partials.reports._empty-row', ['colspan' => count($headers), 'message' => __('This report is not yet implemented.')])
                @endif
            </tbody>
        </table>
    </div>
</div>

@include('advancereport::partials.reports._pagination', ['paginator' => $data['paginator'] ?? null])
