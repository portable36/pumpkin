@extends('admin.layouts.app')
@section('page_title', __('Advance Reports') . ' - ' . $report['name'])
@section('css')
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/Responsive-2.2.5/css/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/AdvanceReport/Resources/assets/css/style.css') }}">
@endsection
@section('content')
<div class="col-sm-12 list-container">
    <div class="report-page-header">
        <div class="report-header-left-section">
            <a href="{{ route('advance-reports') }}" class="report-back-link">
                <i class="fa fa-arrow-left"></i> {{ __('Back to Reports') }}
            </a>
            <h1 class="report-page-title">{{ $report['name'] }}</h1>
        </div>
        <div class="report-header-right-section">
            <div class="report-header-search-wrapper">
                <i class="fa fa-search report-header-search-icon"></i>
                <input type="text" class="report-header-search-input" id="reportTableSearch" placeholder="{{ __('Search in table') }}...">
                <button type="button" class="report-header-search-clear" id="reportTableSearchClear" style="display: none;" title="{{ __('Clear search') }}">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            @php
                $exportFormatterFactory = app(\Modules\AdvanceReport\Services\Export\ExportFormatterFactory::class);
                $hasExport = $exportFormatterFactory->hasExport($report['slug']);
            @endphp
            @if($hasExport)
            <a href="{{ route('advance-reports.export', $report['slug']) }}?{{ http_build_query(request()->all()) }}" class="report-export-btn" id="exportBtn" title="{{ __('Export to CSV') }}">
                <i class="fa fa-download"></i> {{ __('Export CSV') }}
            </a>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7 col-md-7 col-lg-8 col-xl-9 mt-3">
            <div class="bg-white p-4" id="report-module">
                <div id="report-content" class="report-content-wrapper">
                    @include('advancereport::partials.report-data', ['data' => $data, 'report' => $report])
                    <div id="report-loading-overlay" class="report-loading-overlay" style="display: none;">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5 col-md-5 col-lg-4 col-xl-3 px-2 mt-3">
            <div class="filter-sidebar-container bg-white">
                <div class="filter-header">
                    <h3 class="filter-title">{{ __('Filters') }}</h3>
                    <div class="filter-separator"></div>
                </div>
                <form action="{{ route('advance-reports.show', $report['slug']) }}" method="get" class="form-horizontal" id="reportForm">
                    <div class="filter-body">
                        <div class="form-group filter-data date-picker-field" id="date-picker-field">
                            <label for="date-range" class="filter-label">{{ __('Date range') }}</label>
                            <div class="date-range-input-wrapper">
                                <i class="fa fa-calendar date-range-icon"></i>
                                <input type="text" class="date-range-input" id="daterange-btn" readonly placeholder="{{ __('Select date range') }}">
                            </div>
                            <input class="form-control" id="startfrom" type="hidden" name="from" value="{{ $fromDate }}">
                            <input class="form-control" id="endto" type="hidden" name="to" value="{{ $toDate }}">
                        </div>
                        <div class="form-group filter-data vendor-field mb-40" id="vendor-field">
                            <label for="vendor_id" class="filter-label">{{ __('Vendor') }}</label>
                            <select class="form-control select2 filter-select" id="vendor_id" name="vendor_id">
                                <option value="">{{ __('All Vendors') }}</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ isset($vendorId) && $vendorId == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if($report['slug'] === 'payments-by-order')
                        <div class="form-group filter-data payment-status-field mb-45 mt-65" id="payment-status-field">
                            <label for="payment_status" class="filter-label">{{ __('Payment Status') }}</label>
                            <select class="form-control select2 filter-select" id="payment_status" name="payment_status">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="Paid" {{ isset($paymentStatus) && $paymentStatus === 'Paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                <option value="Unpaid" {{ isset($paymentStatus) && $paymentStatus === 'Unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                                <option value="Partial" {{ isset($paymentStatus) && $paymentStatus === 'Partial' ? 'selected' : '' }}>{{ __('Partially Paid') }}</option>
                            </select>
                        </div>
                        @endif
                        @if($report['slug'] === 'total-sales-by-order')
                        <div class="form-group filter-data payment-status-field mb-40 mt-65" id="payment-status-field">
                            <label for="payment_status" class="filter-label">{{ __('Payment Status') }}</label>
                            <select class="form-control select2 filter-select" id="payment_status" name="payment_status">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="Paid" {{ isset($paymentStatus) && $paymentStatus === 'Paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                <option value="Unpaid" {{ isset($paymentStatus) && $paymentStatus === 'Unpaid' ? 'selected' : '' }}>{{ __('Unpaid') }}</option>
                                <option value="Partial" {{ isset($paymentStatus) && $paymentStatus === 'Partial' ? 'selected' : '' }}>{{ __('Partially Paid') }}</option>
                            </select>
                        </div>
                        <div class="form-group filter-data order-status-field mb-40 mt-65" id="order-status-field">
                            <label for="order_status" class="filter-label">{{ __('Order Status') }}</label>
                            <select class="form-control select2 filter-select" id="order_status" name="order_status">
                                <option value="">{{ __('All Statuses') }}</option>
                                @php
                                    $orderStatuses = \App\Models\OrderStatus::getAll();
                                @endphp
                                @foreach($orderStatuses as $status)
                                    <option value="{{ $status->id }}" {{ isset($orderStatus) && $orderStatus == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group filter-data channel-field mb-45 mt-65" id="channel-field">
                            <label for="channel" class="filter-label">{{ __('Channel') }}</label>
                            <select class="form-control select2 filter-select" id="channel" name="channel">
                                <option value="">{{ __('All Channels') }}</option>
                                <option value="web" {{ isset($channel) && $channel === 'web' ? 'selected' : '' }}>{{ __('Web') }}</option>
                                <option value="pos" {{ isset($channel) && $channel === 'pos' ? 'selected' : '' }}>{{ __('POS') }}</option>
                                <option value="invoice" {{ isset($channel) && $channel === 'invoice' ? 'selected' : '' }}>{{ __('Invoice') }}</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    <div class="filter-footer mt-4">
                        <button type="submit" class="btn btn-filter-apply" id="applyFilterBtn">
                            <span class="btn-text">{{ __('Apply') }}</span>
                            <span class="btn-loading-spinner" style="display: none;">
                                <span class="spinner-border spinner-border-sm ml-2" role="status" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script type="text/javascript">
        'use strict';
        var startDate = @json($fromDate ?? 'undefined');
        var endDate = @json($toDate ?? 'undefined');
        var currentSearch = @json(request()->get('search', ''));
    </script>
        <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
        <script src="{{ asset('public/dist/plugins/DataTables-1.10.21/js/jquery.dataTablesCus.min.js') }}"></script>
        <script src="{{ asset('public/dist/plugins/Responsive-2.2.5/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"></script>
        <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report-config.min.js') }}"></script>
        <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/common.min.js') }}"></script>
        <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/table.min.js') }}"></script>
        <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report-show.min.js') }}"></script>
        <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report-list.min.js') }}"></script>
        <script src="{{ asset('Modules/AdvanceReport/Resources/assets/js/report.min.js') }}"></script>
@endsection
