@extends('admin.layouts.app')
@section('page_title', __('OTP Logs Summary'))
@section('content')
    <div class="col-sm-12 list-container" id="otp-logs-container">
        <div class="card">
            <div class="card-header bb-none pb-0">
                <h5>{{ __('OTP Logs Summary') }}</h5>
                <x-backend.group-filters :groups="$groups" :column="'channel'" />
                <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                    <x-backend.button.filter />
                </div>
            </div>

            <x-backend.datatable.filter-panel>
                <div class="col-md-3">
                    <x-backend.datatable.input-search />
                </div>
                <div class="col-md-3">
                    <div class="input-group h-100">
                        <button type="button" class="form-control date-drop-down" id="daterange-btn">
                            <span class="ltr:float-left rtl:float-right"><i class="fa fa-calendar"></i> {{ __('Date range picker') }}</span>
                            <i class="fa fa-caret-down ltr:float-right rtl:float-left pt-1"></i>
                        </button>
                    </div>
                </div>
                <input class="form-control" id="startfrom" type="hidden" name="from">
                <input class="form-control" id="endto" type="hidden" name="to">
                <select class="filter display-none" name="start_date" id="start_date"></select>
                <select class="filter display-none" name="end_date" id="end_date"></select>
            </x-backend.datatable.filter-panel>

            <x-backend.datatable.table-wrapper class="otp-logs-summary-processing-message"
                data-namespace="\App\Models\OtpLog" data-column="id">
                @include('admin.layouts.includes.yajra-data-table')
            </x-backend.datatable.table-wrapper>
        </div>
    </div>
    @include('admin.layouts.includes.delete-modal')
@endsection

@section('js')
    <script src="{{ asset('public/dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/dist/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"></script>
    <script type="text/javascript">
        'use strict';
        var startDate = @json($from ?? 'undefined');
        var endDate   = @json($to ?? 'undefined');
    </script>
    <script src="{{ asset('public/dist/js/custom/otp-logs.min.js') }}"></script>
@endsection

