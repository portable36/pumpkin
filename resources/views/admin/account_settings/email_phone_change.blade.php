@extends('admin.layouts.app')
@section('page_title', __('Email/Phone Change Settings'))
@section('css')

@endsection

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="account-settings-container">
        <div class="card">
            <div class="card-body row">
                <div
                    class="col-lg-3 col-12 z-index-10 ltr:ps-md-3 ltr:pe-0 ltr:ps-0 rtl:pe-md-3 rtl:ps-0 rtl:pe-0">
                    @include('admin.layouts.includes.account_settings_menu')
                </div>
                <div class="col-lg-9 col-12 ltr:ps-0 rtl:pe-0">
                    <div class="card card-info shadow-none mb-0">
                        <div class="card-header p-t-20 border-bottom">
                            <h5>{{ __('Email/Phone Change Settings') }}</h5>
                        </div>
                        <div class="card-block table-border-style">
                            <form action="{{ route('account.setting.emailPhoneChange') }}" method="post" class="form-horizontal"
                                id="email_phone_change_form">
                                @csrf
                                <div class="card-body p-0">
                                    <div class="form-group row">
                                        <label class="col-4 control-label"
                                            for="otp_attempts_limit">{{ __('OTP Attempts Limit (per hour)') }}</label>
                                        <div class="col-6">
                                            <input type="number" name="otp_attempts_limit" 
                                                class="form-control inputFieldDesign" 
                                                value="{{ $otp_attempts_limit }}" 
                                                min="1" max="100" step="1" required>
                                        </div>
                                        <div class="offset-4 col-8">
                                            <small class="text-muted">{{ __('Maximum number of OTP attempts allowed within 1 hour. Default is 5 attempts.') }}</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-4 control-label"
                                            for="email_phone_change_cooldown_days">{{ __('Change Cooldown Period (days)') }}</label>
                                        <div class="col-6">
                                            <input type="number" name="email_phone_change_cooldown_days" 
                                                class="form-control inputFieldDesign" 
                                                value="{{ $email_phone_change_cooldown_days }}" 
                                                min="0" max="365" step="1" required>
                                        </div>
                                        <div class="offset-4 col-8">
                                            <small class="text-muted">{{ __('Number of days a user must wait after successfully changing their email/phone before they can change it again. Set to 0 to allow immediate changes. Default is 30 days.') }}</small>
                                        </div>
                                    </div>
                                    <div class="card-footer p-0">
                                        <div class="form-group row">
                                            <label for="btn_save" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn form-submit custom-btn-submit ltr:float-right rtl:float-left"
                                                    id="footer-btn">
                                                    {{ __('Save') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/datta-able/plugins/sweetalert/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/settings.min.js') }}"></script>
@endsection
