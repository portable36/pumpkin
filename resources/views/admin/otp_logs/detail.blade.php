@extends('admin.layouts.app')
@section('page_title', __('OTP Logs Detail'))
@section('content')
    <div class="col-sm-12 list-container" id="otp-logs-detail-container">
        <div class="card">
            <div class="card-header bb-none pb-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-2">
                            <a href="{{ route('otp-logs.index') }}">{{ __('OTP Logs Summary') }}</a> -> {{ __('OTP Logs Detail') }}
                        </h5>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            @if($user)
                                <div>
                                    <strong>{{ __('User') }}:</strong>
                                    <a href="{{ route('users.edit', ['id' => $user->id]) }}" class="ml-1">
                                        {{ $user->name }}
                                    </a>
                                </div>
                            @endif
                            <div>
                                <span class="badge badge-{{ $contactType == 'Email' ? 'mv-info' : 'mv-success' }}">
                                    {{ $contactType }}: {{ $contact }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-header-right my-2 mx-md-0 mx-sm-4">
                        <x-backend.button.filter />
                    </div>
                </div>
            </div>

            <x-backend.datatable.filter-panel>
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
                @if(!empty($email))
                    <input type="hidden" class="filter" name="email" value="{{ $email }}">
                @endif
                @if(!empty($phone))
                    <input type="hidden" class="filter" name="phone" value="{{ $phone }}">
                @endif
                <div class="col-md-3">
                    <select class="select2-hide-search filter" name="type">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="registration">{{ __('Registration') }}</option>
                        <option value="password_reset">{{ __('Password Reset') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="select2-hide-search filter" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="sent">{{ __('Sent') }}</option>
                        <option value="failed">{{ __('Failed') }}</option>
                    </select>
                </div>
            </x-backend.datatable.filter-panel>

            <x-backend.datatable.table-wrapper class="otp-logs-detail-processing-message"
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

