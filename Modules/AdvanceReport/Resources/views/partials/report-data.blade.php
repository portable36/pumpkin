<div id="report">
    @php
        // Map report slug to view partial
        $reportViewMap = [
            'total-sales-by-product' => 'advancereport::partials.reports.total-sales-by-product',
            'total-sales-over-time' => 'advancereport::partials.reports.total-sales-over-time',
            'sales-by-customer-name' => 'advancereport::partials.reports.sales-by-customer-name',
            'payments-by-order' => 'advancereport::partials.reports.payments-by-order',
            'total-sales-by-order' => 'advancereport::partials.reports.total-sales-by-order',
            'pos-staff-sales-total' => 'advancereport::partials.reports.pos-staff-sales-total',
            'pos-staff-daily-sales-total' => 'advancereport::partials.reports.pos-staff-daily-sales-total',
            'purchase-orders-over-time' => 'advancereport::partials.reports.purchase-orders-over-time',
        ];
        
        // Get the view partial for this report
        $reportView = isset($report['slug']) ? ($reportViewMap[$report['slug']] ?? null) : null;
    @endphp
    
    @if($reportView && view()->exists($reportView))
        @include($reportView, ['data' => $data, 'report' => $report])
    @else
        {{-- Default fallback for unimplemented reports --}}
        <div class="table-wrapper" id="tableWrapper">
            <div class="table-scroll-container" id="tableContainer">
                <table class="table advance-report-table display nowrap comparison-table" id="mainTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('Data') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="report-empty-row">
                            <td colspan="1">
                                <div class="report-no-data-message">
                                    <div class="report-message-content">
                                        <div class="report-message-icon-wrapper">
                                            <i class="fa fa-info-circle report-message-icon"></i>
                                        </div>
                                        <p class="report-message-text">
                                            {{ $data['message'] ?? __('This report is not yet implemented.') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script defer src="{{ asset('Modules/AdvanceReport/Resources/assets/js/render-report.min.js') }}"></script>
