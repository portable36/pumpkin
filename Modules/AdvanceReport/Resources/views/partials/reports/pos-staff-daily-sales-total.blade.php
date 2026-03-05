{{-- POS Staff Daily Sales Total Report --}}
@php
    $headers = [
        ['column' => 'date', 'label' => __('Date'), 'align' => 'left', 'sortable' => true, 'active' => 'active-desc'],
        ['column' => 'order_count', 'label' => __('Number of Orders'), 'align' => 'right', 'sortable' => true],
        ['column' => 'total_sales', 'label' => __('Total Sales'), 'align' => 'right', 'sortable' => true],
        ['column' => 'avg_order_value', 'label' => __('Average Order Value'), 'align' => 'right', 'sortable' => true],
    ];
@endphp
<div class="table-wrapper" id="tableWrapper">
    <!-- Sticky header that appears when scrolling -->
    <div class="sticky-header" id="stickyHeader">
        <div class="sticky-header-inner" id="stickyHeaderInner">
            <table class="table advance-report-table display nowrap jquery-sticky-table comparison-table" id="advanceReportTable">
                <thead>
                    <tr>
                        @foreach ($headers as $header)
                            <th class="{{ $header['sortable'] ? 'sortable' : '' }}{{ isset($header['active']) ? ' ' . $header['active'] : '' }}{{ $header['align'] == 'right' ? ' text-right' : '' }}"
                                data-column="{{ $header['column'] }}">
                                {{ $header['label'] }}
                            </th>
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
                    @foreach ($headers as $header)
                        <th class="{{ isset($header['active']) ? $header['active'] : '' }}{{ $header['align'] == 'right' ? ' text-right' : '' }}">
                            {{ $header['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(isset($data['message']))
                    @include('advancereport::partials.reports._empty-row', ['colspan' => 4, 'message' => $data['message']])
                @elseif(isset($data['comparisonData']))
                    @if(count($data['comparisonData']) > 0)
                        {{-- Summary Rows --}}
                        <tr class="summary-row summary-current" data-row-type="summary">
                            <td class="summary-label" data-sort="">
                                <div>{{ $data['currentPeriodLabel'] ?? __('Current Period') }}</div>
                                <div class="text-muted small">{{ $data['previousPeriodLabel'] ?? __('Previous Period') }}</div>
                                <div>{{ __('% Change') }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $data['totalOrders'] }}">
                                <div>{{ $data['totalOrders'] }}</div>
                                <div class="text-muted small">{{ $data['previousTotalOrders'] }}</div>
                                <strong class="percentage-change percentage-{{ $data['ordersChange']['direction'] ?? 'neutral' }}">
                                    @if(isset($data['ordersChange']['direction']))
                                        @if($data['ordersChange']['direction'] === 'up')
                                            ↗ {{ number_format($data['ordersChange']['value'] ?? 0, 0) }}%
                                        @elseif($data['ordersChange']['direction'] === 'down')
                                            ↘ {{ number_format($data['ordersChange']['value'] ?? 0, 0) }}%
                                        @else
                                            —
                                        @endif
                                    @else
                                        —
                                    @endif
                                </strong>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $data['totalSales'] }}">
                                <div>{{ formatNumber($data['totalSales']) }}</div>
                                <div class="text-muted small">{{ formatNumber($data['previousTotalSales']) }}</div>
                                <strong class="percentage-change percentage-{{ $data['salesChange']['direction'] ?? 'neutral' }}">
                                    @if(isset($data['salesChange']['direction']))
                                        @if($data['salesChange']['direction'] === 'up')
                                            ↗ {{ number_format($data['salesChange']['value'] ?? 0, 0) }}%
                                        @elseif($data['salesChange']['direction'] === 'down')
                                            ↘ {{ number_format($data['salesChange']['value'] ?? 0, 0) }}%
                                        @else
                                            —
                                        @endif
                                    @else
                                        —
                                    @endif
                                </strong>
                            </td>
                            <td class="text-right" data-sort="{{ $data['avgOrderValue'] }}">
                                <div>{{ formatNumber($data['avgOrderValue']) }}</div>
                                <div class="text-muted small">{{ formatNumber($data['previousAvgOrderValue']) }}</div>
                                <strong class="percentage-change percentage-{{ $data['avgOrderValueChange']['direction'] ?? 'neutral' }}">
                                    @if(isset($data['avgOrderValueChange']['direction']))
                                        @if($data['avgOrderValueChange']['direction'] === 'up')
                                            ↗ {{ number_format($data['avgOrderValueChange']['value'] ?? 0, 0) }}%
                                        @elseif($data['avgOrderValueChange']['direction'] === 'down')
                                            ↘ {{ number_format($data['avgOrderValueChange']['value'] ?? 0, 0) }}%
                                        @else
                                            —
                                        @endif
                                    @else
                                        —
                                    @endif
                                </strong>
                            </td>
                        </tr>
                        
                        {{-- Comparison Data --}}
                        @foreach($data['comparisonData'] as $comparison)
                        <tr class="comparison-row">
                            <td class="font-weight-500" data-sort="{{ $comparison['current_date'] }}">
                                @php
                                    $groupBy = $data['groupBy'] ?? 'day';
                                    $currentDate = $comparison['current_date'];
                                    $previousDate = $comparison['previous_date'];
                                    
                                    if ($groupBy === 'day') {
                                        $currentFormatted = formatDate($currentDate);
                                        $previousFormatted = formatDate($previousDate);
                                    } elseif ($groupBy === 'month') {
                                        // Format as "Jan 2025"
                                        $currentFormatted = \Carbon\Carbon::createFromFormat('Y-m', $currentDate)->format('M Y');
                                        $previousFormatted = \Carbon\Carbon::createFromFormat('Y-m', $previousDate)->format('M Y');
                                    } else {
                                        // Year only
                                        $currentFormatted = $currentDate;
                                        $previousFormatted = $previousDate;
                                    }
                                @endphp
                                <div>{{ $currentFormatted }}</div>
                                <div class="text-muted small">{{ $previousFormatted }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $comparison['current']['order_count'] }}">
                                <div>{{ $comparison['current']['order_count'] }}</div>
                                <div class="text-muted small">{{ $comparison['previous']['order_count'] }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $comparison['current']['total_sales'] }}">
                                <div>{{ formatNumber($comparison['current']['total_sales']) }}</div>
                                <div class="text-muted small">{{ formatNumber($comparison['previous']['total_sales']) }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $comparison['current']['avg_order_value'] }}">
                                <div>{{ formatNumber($comparison['current']['avg_order_value']) }}</div>
                                <div class="text-muted small">{{ formatNumber($comparison['previous']['avg_order_value']) }}</div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        @include('advancereport::partials.reports._empty-row', ['colspan' => 4, 'message' => __('No sales data found.')])
                    @endif
                @else
                    @include('advancereport::partials.reports._empty-row', ['colspan' => 4, 'message' => __('This report is not yet implemented.')])
                @endif
            </tbody>
        </table>
    </div>
</div>

