@extends('admin.layouts.app')
@section('page_title', __('Delivery Settings'))

@section('css')
    <link rel="stylesheet" href="{{ asset('public/datta-able/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/Delivery/Resources/assets/css/delivery-module.min.css') }}">
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card" id="delivery-settings-container">
            <div class="card-body row">
                <div class="col-lg-3 col-12 z-index-10 pe-0 ps-0 ps-md-3" aria-labelledby="navbarDropdown">
                    <div class="card card-info shadow-none">
                        <div class="card-header p-t-20 border-bottom mb-2">
                            <h5>{{ __('Delivery Settings') }}</h5>
                        </div>
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <li><a class="nav-link active text-left tab-name" id="v-pills-general-tab" data-bs-toggle="pill"
                                    href="#v-pills-general" role="tab" aria-controls="v-pills-general"
                                    aria-selected="true" data-id={{ __('Options') }}>{{ __('Options') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 col-12 ps-0">
                    <div class="card card-info shadow-none mb-0">
                        <div class="card-header p-t-20 border-bottom">
                            <h5>{{ __('Options') }}</h5>
                        </div>

                        <div class="tab-content" id="topNav-v-pills-tabContent">
                            {{-- Options --}}
                            <div class="tab-pane fade parent show active" id="v-pills-general" role="tabpanel"
                                aria-labelledby="v-pills-general-tab">
                                <div class="noti-alert pad no-print warningMessage mt-2">
                                    <div class="alert warning-message abc">
                                        <strong id="warning-msg" class="msg"></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <form action="{{ route('admin.delivery.setting_store') }}" method="post"
                                            class="form-horizontal">
                                            @csrf
                                            <div class="card-body border-bottom table-border-style p-0">
                                                <div class="form-tabs">
                                                    <div class="tab-content box-shadow-unset px-0 py-2">
                                                        <div class="form-group row align-items-center">
                                                            <label for="compare"
                                                                class="col-sm-3 control-label text-start">{{ __('Vendor can assign delivery boy') }}</label>
                                                            <div class="col-9 d-flex">
                                                                <div class="me-3">
                                                                    <input type="hidden" name="vendor_can_assign_delivery_man" value="0">
                                                                    <div class="switch switch-bg d-inline m-r-10">
                                                                        <input type="checkbox"
                                                                            name="vendor_can_assign_delivery_man"
                                                                            id="vendor_assign" class="checkActivity"
                                                                            value="1"
                                                                            {{ isset($setting['vendor_can_assign_delivery_man']) && $setting['vendor_can_assign_delivery_man'] == 1 ? 'checked' : '' }}>
                                                                        <label for="vendor_assign" class="cr"></label>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-2">
                                                                    <span>{{ __('Enable') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label for="notification-type"
                                                                class="col-sm-3 control-label text-left">{{ __('Notification') }}</label>
                                                            <div class="col-sm-4">
                                                                <select class="form-control select2 select2-hide-search"
                                                                    id="notification-type"
                                                                    name="notification_type_delivery_man">
                                                                    <option value="none"
                                                                        {{ isset($setting['notification_type_delivery_man']) && $setting['notification_type_delivery_man'] == 'none' ? 'selected' : '' }}>
                                                                        None</option>
                                                                    <option value="mail"
                                                                        {{ isset($setting['notification_type_delivery_man']) && $setting['notification_type_delivery_man'] == 'mail' ? 'selected' : '' }}>
                                                                        Mail</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="payment-type"
                                                                class="col-sm-3 control-label">{{ __('Payment') }}</label>
                                                            <div class="col-sm-4">
                                                                <select class="form-control select2-hide-search"
                                                                    id="payment-type" name="payment_type_delivery_man">
                                                                    <option value="salary"
                                                                        {{ isset($setting['payment_type_delivery_man']) && $setting['payment_type_delivery_man'] == 'salary' ? 'selected' : '' }}>
                                                                        {{ __('salary') }}</option>
                                                                    <option value="commission"
                                                                        {{ isset($setting['payment_type_delivery_man']) && $setting['payment_type_delivery_man'] == 'commission' ? 'selected' : '' }}>
                                                                        {{ __('percentage') }}</option>
                                                                    <option value="flat"
                                                                        {{ isset($setting['payment_type_delivery_man']) && $setting['payment_type_delivery_man'] == 'flat' ? 'selected' : '' }}>
                                                                        {{ __('flat') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="commission">
                                                            <label for="commission_percentage"
                                                                class="col-sm-3 control-label text-start">{{ __('Commissoin (In Percentage)') }}</label>
                                                            <div class="col-sm-4">
                                                                <input type="number" name="commission_percentage"
                                                                    id="commission_percentage" class="form-control form-height"
                                                                    min='0' max='100' step='1'
                                                                    value="{{ isset($setting['commission_percentage']) ? $setting['commission_percentage'] : 0 }}"
                                                                    oninput="validity.valid||(value='');">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row" id="flat">
                                                            <label for="commission_flat"
                                                                class="col-sm-3 control-label text-start">{{ __('Commissoin (In flat)') }}</label>
                                                            <div class="col-sm-4">
                                                                <input type="number" name="commission_flat"
                                                                    id="commission_flat" class="form-control form-height"
                                                                    min='0' max='100' step='1'
                                                                    value="{{ isset($setting['commission_flat']) ? $setting['commission_flat'] : 0 }}"
                                                                    oninput="validity.valid||(value='');">
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer p-0">
                                                <div class="form-group row">
                                                    <label for="btn_save" class="col-sm-3 control-label"></label>
                                                    <div class="col-sm-12">
                                                        <button type="submit"
                                                            class="btn form-submit custom-btn-submit float-right save-button"
                                                            id="footer-btn">
                                                            <span
                                                                class="d-none product-spinner spinner-border spinner-border-sm text-secondary"
                                                                role="status"></span>
                                                            {{ __('Save') }}
                                                        </button>
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
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('public/datta-able/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/Delivery/Resources/assets/js/delivery-settings.min.js') }}"></script>
@endsection
