@extends('admin.layouts.app')
@section('page_title', __('Add Delivery Boy'))

@section('css')
    <link rel="stylesheet" href="{{ asset('Modules/MediaManager/Resources/assets/css/media-manager.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/Delivery/Resources/assets/css/delivery-module.min.css') }}">
@endsection

@php
    $carrierDefaultSignupStatus = preference('carrier_default_signup_status');
@endphp

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="carrier-add-container">
        <div class="card">
            <div class="card-header">
                <h5> <a href="{{ route('admin.delivery.carrier.index') }}">{{ __('Delivery Boys') }} </a>
                    >>{{ __('Create :x', ['x' => __('Delivery Boy')]) }}</h5>
            </div>
            <div class="card-block table-border-style">
                <div class="row form-tabs">
                    <form action="{{ route('admin.delivery.carrier.store') }}" method="post" id="carrierAdd"
                        class="form-horizontal col-sm-12" enctype="multipart/form-data"
                        onsubmit="return passwordValidation()">
                        @csrf
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link font-bold active text-uppercase" id="home-tab" data-bs-toggle="tab"
                                    href="#home" role="tab" aria-controls="home"
                                    aria-selected="true">{{ __(':x Information', ['x' => __('Delivery Boy')]) }}</a>
                            </li>
                        </ul>
                        <div class="col-sm-12 tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div class="form-group row">
                                            <label for="name"
                                                class="col-sm-3 form-label require">{{ __('Name') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Name') }}"
                                                    class="form-control inputFieldDesign" id="name" name="name"
                                                    value="{{ old('name') }}" maxlength="80"
                                                    oninvalid="this.setCustomValidity({{ __('This field is required.') }})"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email"
                                                class="col-sm-3 form-label require">{{ __('Email') }}</label>
                                            <div class="col-sm-9">
                                                    <input type="email"
                                                    class="form-control form-width inputFieldDesign bg-white" id="email"
                                                    name="email" value="{{ old('email') }}"
                                                    placeholder="{{ __('Email') }}" required
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                    data-type-mismatch="{{ __('Enter a valid :x.', ['x' => strtolower(__('Email'))]) }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                class="col-sm-3 form-label require">{{ __('Password') }}</label>
                                            <div class="col-sm-9">
                                                <input type="password" placeholder="{{ __('Password') }}"
                                                    class="form-control password-validation inputFieldDesign" id="password"
                                                    name="password" value="" maxlength="190"
                                                    oninvalid="this.setCustomValidity({{ __('This field is required.') }})"
                                                    required>
                                                <div class="password-validation-error mt-1"></div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="phone"
                                                class="col-sm-3 form-label require">{{ __('Phone') }}</label>
                                            <div class="col-sm-9">                                                                                          
                                                <input type="tel" placeholder="{{ __('Phone') }}"
                                                    class="form-control phone-number inputFieldDesign"
                                                    id="phone" name="phone"
                                                    pattern="\d{10,15}"
                                                    value="{{ old('phone') }}"
                                                    maxlength="15"
                                                    oninput="this.value = this.value.replace(/\D/g, '')"
                                                    oninvalid="this.setCustomValidity('{{ __('Please enter a valid phone number.') }}')"
                                                    oninput="this.setCustomValidity('')"
                                                    required>
                                             
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="Gender"
                                                class="col-sm-3 form-label">{{ __('Gender') }}</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2-hide-search inputFieldDesign"
                                                    name="gender" id="gender">
                                                    <option value="Male" selected>{{ __('Male') }}</option>
                                                    <option value="Female">{{ __('Female') }}</option>
                                                    <option value="Other">{{ __('Other') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="address"
                                                class="col-sm-3 form-label require">{{ __('Address') }}</label>
                                            <div class="col-sm-9">
                                                <textarea placeholder="{{ __('Address') }}" id="address" class="form-control" name="address" minlength="5"
                                                    data-min-length="{{ __('Address should be at least 5 characters.') }}" maxlength="191"
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" required>{{ old('phone') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row preview-parent">
                                            <label class="col-sm-3 form-label">{{ __('Driving License') }}</label>
                                            <div class="col-sm-9">
                                                <div class="custom-file position-relative media-manager-img"
                                                    data-val="single" data-returntype="ids" id="image-status" data-type="{{ implode(',', getFileExtensions(3)) }}">
                                                    <input class="custom-file-input is-image form-control inputFieldDesign"
                                                        name="custom_file_input" accept="{{ implode(',', getFileExtensions(3)) }}">
                                                    <label
                                                        class="custom-file-label overflow_hidden d-flex align-items-center"
                                                        for="validatedCustomFile">{{ __('Upload image') }}</label>
                                                </div>
                                                <div class="preview-image" id="#">
                                                    <!-- img will be shown here -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" id="divNote">
                                            <label class="col-sm-3 form-label"></label>
                                            <div class="col-sm-9" id='note_txt_1'>
                                                <div class="d-flex">
                                                    <span
                                                        class="badge badge-danger h-100 mt-1">{{ __('Note') }}!</span>
                                                    <ul>
                                                        <li>
                                                            {{ __('Allowed File Extensions: :y and Maximum File Size :x', ['x' => preference('file_size') . 'MB.', 'y' => implode(',', getFileExtensions(3))]) }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="license_verification_status"
                                                class="col-sm-3 form-label">{{ __('License Verification Status') }}</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2-hide-search inputFieldDesign"
                                                    name="license_status" id="license_verification_status">
                                                    <option value="pending"
                                                        {{ old('license_status') == 'pending' || $carrierDefaultSignupStatus == 'pending' ? 'selected' : '' }}>
                                                        {{ __('Pending') }}
                                                    </option>
                                                    <option value="verified"
                                                        {{ old('license_status') == 'verified' || $carrierDefaultSignupStatus == 'verified' ? 'selected' : '' }}>
                                                        {{ __('Verified') }}
                                                    </option>
                                                    <option value="invalid"
                                                        {{ old('license_status') == 'invalid' || $carrierDefaultSignupStatus == 'invalid' ? 'selected' : '' }}>
                                                        {{ __('Invalid') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="active_status"
                                                class="col-sm-3 form-label">{{ __('Available') }} </label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2-hide-search inputFieldDesign"
                                                    name="is_active" id="availability_status">
                                                    <option value="0"
                                                        {{ old('is_active') == '0' ? 'selected' : '' }}>
                                                        {{ __('No') }}
                                                    </option>
                                                    <option value="1"
                                                        {{ old('is_active') == '1' ? 'selected' : '' }}>
                                                        {{ __('Yes') }} 
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-9 offset-sm-3">
                                                <div class="checkbox checkbox-primary checkbox-fill d-inline">
                                                    <input type="checkbox" class="form-control" name="send_mail"
                                                        id="checkbox-p-fill-1">
                                                    <label for="checkbox-p-fill-1"
                                                        class="cr">{{ __('Send email to the vendor') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-10 px-0 mt-3 mt-md-0">
                                <a href="{{ route('admin.delivery.carrier.index') }}"
                                    class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
                                <button class="btn custom-btn-submit" type="submit" id="btnSubmit"><i
                                        class="comment_spinner spinner fa fa-spinner fa-spin custom-btn-small display_none"></i><span
                                        id="spinnerText">{{ __('Save') }}</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('mediamanager::image.modal_image')
@endsection
@php
    $uppercase = $lowercase = $number = $symbol = $length = 0;
    if (env('PASSWORD_STRENGTH') != null && env('PASSWORD_STRENGTH') != '') {
        $length = filter_var(env('PASSWORD_STRENGTH'), FILTER_SANITIZE_NUMBER_INT);
        $conditions = explode('|', env('PASSWORD_STRENGTH'));
        $uppercase = in_array('UPPERCASE', $conditions);
        $lowercase = in_array('LOWERCASE', $conditions);
        $number = in_array('NUMBERS', $conditions);
        $symbol = in_array('SYMBOLS', $conditions);
    }
@endphp
@section('js')
    <script>
        'use strict';
        var uppercase = "{!! $uppercase !!}";
        var lowercase = "{!! $lowercase !!}";
        var number = "{!! $number !!}";
        var symbol = "{!! $symbol !!}";
        var length = "{!! $length !!}";
        var currentUrl = "{!! url()->full() !!}";
        var loginNeeded = "{!! session('loginRequired') ? 1 : 0 !!}";
    </script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('Modules/Delivery/Resources/assets/js/carrier.min.js') }}"></script>
@endsection
