{{-- Purchase Orders Over Time Report --}}
@php
    $headers = [
        ['column' => 'date', 'label' => __('Date'), 'align' => 'left', 'sortable' => true, 'active' => 'active-desc'],
        ['column' => 'purchase_count', 'label' => __('Number of Purchase Orders'), 'align' => 'right', 'sortable' => true],
        ['column' => 'total_product', 'label' => __('Total Product'), 'align' => 'right', 'sortable' => true],
        ['column' => 'total_amount', 'label' => __('Total Amount'), 'align' => 'right', 'sortable' => true],
        ['column' => 'avg_purchase_value', 'label' => __('Average Purchase Value'), 'align' => 'right', 'sortable' => true],
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
                    @include('advancereport::partials.reports._empty-row', ['colspan' => 5, 'message' => $data['message']])
                @elseif(isset($data['comparisonData']))
                    @if(count($data['comparisonData']) > 0)
                        {{-- Summary Rows --}}
                        <tr class="summary-row summary-current" data-row-type="summary">
                            <td class="summary-label" data-sort="">
                                <div>{{ $data['currentPeriodLabel'] ?? __('Current Period') }}</div>
                                <div class="text-muted small">{{ $data['previousPeriodLabel'] ?? __('Previous Period') }}</div>
                                <div>{{ __('% Change') }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $data['totalPurchases'] }}">
                                <div>{{ $data['totalPurchases'] }}</div>
                                <div class="text-muted small">{{ $data['previousTotalPurchases'] }}</div>
                                <strong class="percentage-change percentage-{{ $data['purchasesChange']['direction'] ?? 'neutral' }}">
                                    @if(isset($data['purchasesChange']['direction']))
                                        @if($data['purchasesChange']['direction'] === 'up')
                                            ↗ {{ number_format($data['purchasesChange']['value'] ?? 0, 0) }}%
                                        @elseif($data['purchasesChange']['direction'] === 'down')
                                            ↘ {{ number_format($data['purchasesChange']['value'] ?? 0, 0) }}%
                                        @else
                                            —
                                        @endif
                                    @else
                                        —
                                    @endif
                                </strong>
                            </td>
                            <td class="text-right" data-sort="{{ $data['totalProduct'] }}">
                                <div>{{ formatNumber($data['totalProduct'], ' ') }}</div>
                                <div class="text-muted small">{{ formatNumber($data['previousTotalProduct'], ' ') }}</div>
                                <strong class="percentage-change percentage-{{ $data['productChange']['direction'] ?? 'neutral' }}">
                                    @if(isset($data['productChange']['direction']))
                                        @if($data['productChange']['direction'] === 'up')
                                            ↗ {{ number_format($data['productChange']['value'] ?? 0, 0) }}%
                                        @elseif($data['productChange']['direction'] === 'down')
                                            ↘ {{ number_format($data['productChange']['value'] ?? 0, 0) }}%
                                        @else
                                            —
                                        @endif
                                    @else
                                        —
                                    @endif
                                </strong>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $data['totalAmount'] }}">
                                <div>{{ formatNumber($data['totalAmount']) }}</div>
                                <div class="text-muted small">{{ formatNumber($data['previousTotalAmount']) }}</div>
                                <strong class="percentage-change percentage-{{ $data['amountChange']['direction'] ?? 'neutral' }}">
                                    @if(isset($data['amountChange']['direction']))
                                        @if($data['amountChange']['direction'] === 'up')
                                            ↗ {{ number_format($data['amountChange']['value'] ?? 0, 0) }}%
                                        @elseif($data['amountChange']['direction'] === 'down')
                                            ↘ {{ number_format($data['amountChange']['value'] ?? 0, 0) }}%
                                        @else
                                            —
                                        @endif
                                    @else
                                        —
                                    @endif
                                </strong>
                            </td>
                            <td class="text-right" data-sort="{{ $data['avgPurchaseValue'] }}">
                                <div>{{ formatNumber($data['avgPurchaseValue']) }}</div>
                                <div class="text-muted small">{{ formatNumber($data['previousAvgPurchaseValue']) }}</div>
                                <strong class="percentage-change percentage-{{ $data['avgPurchaseValueChange']['direction'] ?? 'neutral' }}">
                                    @if(isset($data['avgPurchaseValueChange']['direction']))
                                        @if($data['avgPurchaseValueChange']['direction'] === 'up')
                                            ↗ {{ number_format($data['avgPurchaseValueChange']['value'] ?? 0, 0) }}%
                                        @elseif($data['avgPurchaseValueChange']['direction'] === 'down')
                                            ↘ {{ number_format($data['avgPurchaseValueChange']['value'] ?? 0, 0) }}%
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
                                        try {
                                            $currentFormatted = \Carbon\Carbon::createFromFormat('Y-m', $currentDate)->format('M Y');
                                            $previousFormatted = \Carbon\Carbon::createFromFormat('Y-m', $previousDate)->format('M Y');
                                        } catch (\Exception $e) {
                                            $currentFormatted = $currentDate;
                                            $previousFormatted = $previousDate;
                                        }
                                    } else {
                                        // Year only
                                        $currentFormatted = $currentDate;
                                        $previousFormatted = $previousDate;
                                    }
                                @endphp
                                <div>{{ $currentFormatted }}</div>
                                <div class="text-muted small">{{ $previousFormatted }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $comparison['current']['purchase_count'] }}">
                                <div>{{ $comparison['current']['purchase_count'] }}</div>
                                <div class="text-muted small">{{ $comparison['previous']['purchase_count'] }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $comparison['current']['total_product'] }}">
                                <div>{{ formatNumber($comparison['current']['total_product'], ' ') }}</div>
                                <div class="text-muted small">{{ formatNumber($comparison['previous']['total_product'], ' ') }}</div>
                            </td>
                            <td class="text-right font-weight-600" data-sort="{{ $comparison['current']['total_amount'] }}">
                                <div>{{ formatNumber($comparison['current']['total_amount']) }}</div>
                                <div class="text-muted small">{{ formatNumber($comparison['previous']['total_amount']) }}</div>
                            </td>
                            <td class="text-right" data-sort="{{ $comparison['current']['avg_purchase_value'] }}">
                                <div>{{ formatNumber($comparison['current']['avg_purchase_value']) }}</div>
                                <div class="text-muted small">{{ formatNumber($comparison['previous']['avg_purchase_value']) }}</div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        @include('advancereport::partials.reports._empty-row', ['colspan' => 5, 'message' => __('No purchase order data found.')])
                    @endif
                @else
                    @include('advancereport::partials.reports._empty-row', ['colspan' => 5, 'message' => __('This report is not yet implemented.')])
                @endif
            </tbody>
        </table>
    </div>
</div>

