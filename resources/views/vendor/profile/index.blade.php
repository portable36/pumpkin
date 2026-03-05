@extends('vendor.layouts.app')
@section('page_title', __('Vendor Profile'))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/datta-able/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/lightbox/css/lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/MediaManager/Resources/assets/css/media-manager.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/css/intl-tel-input/intlTelInput.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/css/custom/vendor-profile.min.css') }}">
@endsection

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="user-edit-container">
        <div class="card">
            <div class="card-header">
                <h5><a href="{{ url('vendor/dashboard') }}">{{ __('Dashboard') }}</a> >> {{ $user->name }} >> {{ __('Profile') }}</h5>
            </div>
            <div class="card-body px-4" id="no_shadow_on_card">
                @include('admin.layouts.includes.vendor_menu')
                <div class="col-sm-12 m-t-20 form-tabs">
                    <ul class="nav nav-tabs " id="myTab" role="tablist">
                        <li class="nav-item" id="kk">
                            <a class="nav-link active text-uppercase fragment-url font-bold" id="user-info-tab" data-bs-toggle="tab" href="#user-info" role="tab" aria-controls="user-info" aria-selected="true">{{ __(':x Information',['x' => __('User')]) }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fragment-url font-bold" id="password-tab" data-bs-toggle="tab" data-rel="{{ $user->id }}" href="#password" role="tab" aria-controls="password" aria-selected="false">{{ __('Update Password') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fragment-url font-bold" id="vendor-info-tab" data-bs-toggle="tab"
                                data-rel="{{ $user->id }}" href="#vendor-info" role="tab" aria-controls="vendor-info"
                                aria-selected="false">{{ __(':x Information',['x' => __('Vendor')]) }}</a>
                        </li>
                    </ul>
                    <div class="col-sm-12 tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="user-info" role="tabpanel" aria-labelledby="user-info-tab">
                            <form action='{{ route("user.update", ["id" => Auth::user()->id]) }}' method="post" class="form-horizontal" id="userEdit" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group row">
                                            <label for="first_name" class="col-sm-2 col-form-label require pr-0">
                                                {{ __('Name') }}
                                            </label>
                                            <div class="col-sm-10">
                                                <input type="text" placeholder="{{ __('Name') }}" class="form-control inputFieldDesign" id="name" name="name" required minlength="3" value="{{ !empty(old('name')) ? old('name') : $user->name }}" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Name'), 'y' => 3]) }}">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">{{ __('Email') }}</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="email" class="form-control inputFieldDesign" id="email" name="email" value="{{ !empty(old('email')) ? old('email') : $user->email }}" placeholder="{{ __('Email') }}" disabled>
                                                    <button type="button" id="change-email-btn" data-bs-toggle="modal" data-bs-target="#changeEmailModal">{{ $user->email ? __('Change Email') : __('Add Email') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row phone-areaa">
                                            <label for="phone" class="col-sm-2 col-form-label">{{ __('Phone') }}</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="phone" class="form-control inputFieldDesign" value="{{ !empty(old('phone')) ? old('phone') : $user->phone }}" disabled>
                                                    <button type="button" id="change-phone-btn" data-bs-toggle="modal" data-bs-target="#changePhoneModal">{{ $user->phone ? __('Change Phone') : __('Add Phone') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="role_id" class="col-sm-2 control-label">{{ __('Role') }}</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" disabled name="role_ids[]" id="role_id">
                                                    @foreach ($roles as $key => $role)
                                                        <option value="{{ $role->id }}" {{ in_array($role->id, $roleIds) ? 'selected' : '' }}>{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="Status" class="col-sm-2 col-form-label require">{{ __('Status') }}</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" disabled name="status" id="status">
                                                    <option value="Pending" {{ old('status', $user->status) == 'Pending' ? 'selected' : ''}}>{{ __('Pending') }}</option>
                                                    <option value="Active" {{ old('status', $user->status) == 'Active' ? 'selected' : ''}}>{{ __('Active') }}</option>
                                                    <option value="Inactive" {{ old('status', $user->status) == 'Inactive' ? 'selected' : ''}}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="{{ isActive('SaaS') ? 'd-none' : '' }}">
                                            <div class="form-group row">
                                                <label for="designation"
                                                    class="col-sm-2 col-form-label pr-0">{{ __('Designation') }}
                                                </label>
                                                <div class="col-sm-10">
                                                    <input type="text" placeholder="{{ __('Designation') }}"
                                                        class="form-control inputFieldDesign" id="designation" name="designation"
                                                        value="{{ $user->designation }}">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="description"
                                                    class="col-sm-2 col-form-label pr-0">{{ __('Description') }}
                                                </label>
                                                <div class="col-sm-10 editor">
                                                    <textarea class="form-control" name="description" id="description">{{ $user->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="facebook"
                                                    class="col-sm-2 col-form-label pr-0">{{ __('facebook') }}
                                                </label>
                                                <div class="col-sm-10">
                                                    <input type="url" placeholder="https://www.facebook.com"
                                                        class="form-control inputFieldDesign" id="facebook" name="facebook"
                                                        value="{{ $user->facebook }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="twitter" class="col-sm-2 col-form-label pr-0">{{ __('twitter') }}
                                                </label>
                                                <div class="col-sm-10">
                                                    <input type="url" placeholder="https://www.twitter.com"
                                                        class="form-control inputFieldDesign" id="twitter" name="twitter"
                                                        value="{{ $user->twitter }}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="instagram"
                                                    class="col-sm-2 col-form-label pr-0">{{ __('Instagram') }}
                                                </label>
                                                <div class="col-sm-10">
                                                    <input type="url" placeholder="https://www.instagram.com"
                                                        class="form-control inputFieldDesign" id="instagram" name="instagram"
                                                        value="{{ $user->instagram }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row conditional preview-parent">
                                            <label class="col-sm-2 control-label">{{ __('Picture') }}</label>
                                            <div class="col-sm-10">
                                                <div class="custom-file position-relative media-manager-img" data-val="single" data-returntype="ids" id="image-status" data-type="{{ implode(',', getFileExtensions(2)) }}">
                                                    <input class="custom-file-input is-image form-control">
                                                    <label class="custom-file-label overflow_hidden"
                                                        for="validatedCustomFile">{{ __('Upload Image') }}</label>
                                                </div>
                                                <div class="preview-image" id="#">
                                                    <!-- img will be shown here -->
                                                    <div class="d-flex flex-wrap mt-2">
                                                        <div class="position-relative border boder-1 p-1 mr-2 rounded mt-2">
                                                            <img class="upl-img p-1"
                                                                src="{{ $user->fileUrl() }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divNote">
                                            <label class="col-sm-2 control-label"></label>
                                            <div class="col-sm-10" id='note_txt_1'>
                                                <span class="badge badge-danger">{{ __('Note') }}!</span> {{ __('Allowed File Extensions: jpg, png, gif, bmp') }}
                                            </div>
                                            <div class="col-md-9" id='note_txt_2'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-10 px-0 m-l-5">
                                    <a href="{{ route('vendor-dashboard') }}" class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
                                    <button class="btn custom-btn-submit" type="submit" id="btnSubmit">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form action='{{ route("vendor.password", ["id" => Auth::user()->id]) }}' class="form-horizontal" id="password-form" method="POST">
                                        @csrf

                                        <div class="form-group row">
                                            <label for="password" class="col-sm-2 col-form-label require">{{ __('Password') }}</label>
                                            <div class="col-sm-6 position-relative">
                                                <input  id="new_password"
                                                        name="password"
                                                        type="password"
                                                        class="form-control pe-5"
                                                        placeholder="{{ __('Password') }}"
                                                        required
                                                        minlength="5">
                                                <div type="button"
                                                        class="position-absolute top-50 end-0 pe-3 translate-middle-y me-2 toggle-password"
                                                        data-target="new_password">
                                                    <i class="fas fa-eye"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="confirm_password" class="col-sm-2 col-form-label require">{{ __('Confirm Password') }}</label>
                                            <div class="col-sm-6 position-relative">
                                                <input  id="confirm_password"
                                                        name="confirm_password"
                                                        type="password"
                                                        class="form-control pe-5"
                                                        placeholder="{{ __('Confirm Password') }}"
                                                        required
                                                        minlength="5">
                                                <div type="button"
                                                        class="position-absolute top-50 end-0 pe-3 translate-middle-y me-2 toggle-password"
                                                        data-target="confirm_password">
                                                    <i class="fas fa-eye"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-10 px-0 m-l-5">
                                            <button class="btn custom-btn-submit" type="submit" id="btnSubmit1">{{ __('Save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="vendor-info" role="tabpanel" aria-labelledby="vendor-info-tab">
                            <form action='{{ route("vendor.update", ["id" => $vendor->id]) }}' id="vendorUpdateForm" method="post" class="form-horizontal" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group row">
                                            <label for="first_name" class="col-sm-3 col-form-label require pr-0">{{ __('Name') }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" placeholder="{{ __('Name') }}" class="form-control inputFieldDesign" name="name" required minlength="3" value="{{ !empty(old('name')) ? old('name') : $vendor->name }}" oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Name'), 'y' => 3]) }}">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="email" class="col-sm-3 col-form-label require">{{ __('Email') }}</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control inputFieldDesign" name="email" value="{{ !empty(old('email')) ? old('email') : $vendor->email }}" placeholder="{{ __('Email') }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" data-type-mismatch="{{ __('Enter a valid :x.', [ 'x' => strtolower(__('Email'))]) }}">
                                            </div>
                                        </div>

                                        <div class="form-group row phone-area">
                                            <label for="phone" class="col-sm-3 col-form-label require">{{ __('Phone') }}</label>
                                            <div class="col-sm-9">
                                                <input type="phone" class="form-control inputFieldDesign phone" name="phone" value="{{ !empty(old('phone')) ? old('phone') : $vendor->phone }}" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                            </div>
                                        </div>

                                        <div class="form-group row {{ isActive('SaaS') ? 'd-none' : '' }}">
                                            <label for="phone" class="col-sm-3 col-form-label">{{ __('Commission') }}(%)</label>
                                            <div class="col-sm-9">
                                                <span class="form-control inputFieldDesign">{{ $vendor->sell_commissions }}</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="Status" class="col-sm-3 col-form-label require">{{ __('Status') }}</label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" disabled name="status">
                                                    <option value="Pending" {{ old('status', $vendor->status) == 'Pending' ? 'selected' : ''}}>{{ __('Pending') }}</option>
                                                    <option value="Active" {{ old('status', $vendor->status) == 'Active' ? 'selected' : ''}}>{{ __('Active') }}</option>
                                                    <option value="Inactive" {{ old('status', $vendor->status) == 'Inactive' ? 'selected' : ''}}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="{{ isActive('SaaS') ? 'd-none' : '' }}">
                                            <input type="hidden" name="status" value="{{ $vendor->status }}">
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label">{{ __('Description') }}</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" name="description" id="v-description">{{ $vendor->description }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $invoice = json_decode(preference('invoice'));
                                        @endphp
                                        @if (!isActive('SaaS') || $invoice?->general?->invoice_type == 'vendor')
                                            <div class="form-group row preview-parent">
                                                <label class="col-sm-3 control-label">{{ __('Logo') }}</label>
                                                <div class="col-sm-9">
                                                    <div class="custom-file media-manager-img position-relative" data-val="single" data-returntype="ids" id="image-status" data-type="png,jpg,jpeg,webp">
                                                        <input class="custom-file-input is-image form-control" name="vendor_logo" value="{{ $vendor->vendor_logo }}">

                                                        <label class="custom-file-label overflow_hidden"
                                                            for="validatedCustomFile">{{ __('Upload Logo') }}</label>
                                                    </div>
                                                    @if (isActive('SaaS'))
                                                        <small class="form-text text-muted mt-2 d-inline-block">
                                                            <span class="badge badge-primary mt-1">{{ __('Note') }}!</span> {{ __('This logo will be displayed on the invoice PDF.') }}
                                                        </small>
                                                    @endif

                                                    <div class="preview-image" id="#">
                                                        <!-- img will be shown here -->
                                                        <div class="d-flex flex-wrap mt-2">
                                                            <div class="position-relative border boder-1 p-1 mr-2 rounded mt-2">
                                                                <img class="upl-img p-1"
                                                                    src="{{ optional($vendor->logo)->fileUrl() ?? $vendor->fileUrl() }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="{{ isActive('SaaS') ? 'd-none' : '' }}">
                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label"></label>
                                                <div class="col-sm-9" id='note_txt_1'>
                                                    <div class="d-flex">
                                                        <span class="badge badge-danger h-100 mt-1">{{ __('Note') }}!</span>
                                                        <ul>
                                                            <li>{{__('Allowed File Extensions: jpg, png, gif, bmp and Maximum File Size :x MB',['x' => preference('file_size')]) }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row conditional preview-parent">
                                                <label class="col-sm-3 control-label">{{ __('Cover Photo') }}</label>
                                                <div class="col-sm-9">
                                                    <div class="custom-file media-manager-img position-relative" data-val="single" data-returntype="ids" id="image-status" data-type="png,jpg,jpeg,webp">
                                                        <input class="custom-file-input is-image form-control" name="cover_photo" >

                                                        <label class="custom-file-label overflow_hidden"
                                                            for="validatedCustomFile">{{ __('Upload Logo') }}</label>
                                                    </div>
                                                    <div class="preview-image" id="#">
                                                        <!-- img will be shown here -->
                                                        <div class="d-flex flex-wrap mt-2">
                                                            <div class="position-relative border boder-1 p-1 mr-2 rounded mt-2">
                                                                <img class="upl-img p-1"
                                                                    src="{{ optional($vendor->cover)->fileUrl() ?? $vendor->fileUrl() }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 control-label"></label>
                                                <div class="col-sm-9" id='note_txt_1'>
                                                    <div class="d-flex">
                                                        <span class="badge badge-danger h-100 mt-1">{{ __('Note') }}!</span>
                                                        <ul>
                                                            <li>{{ __('Allowed File Extensions: jpg, png, gif, bmp and Maximum File Size :x MB',['x' => preference('file_size')]) }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-10 px-0 m-l-5">
                                    <a href="{{ route('vendor-dashboard') }}" class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
                                    <button class="btn custom-btn-submit" type="submit">{{ __('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    @include('mediamanager::image.modal_image')

    <!-- Change Email Modal -->
    <div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="changeEmailModalLabel">{{ __('Change Email') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <!-- Step 1: Enter Email -->
                    <div id="email-step-1">
                        <h4 class="fw-bold mb-4 text-center">{{ __('Enter New Email') }}</h4>
                        
                        <div class="text-center mb-4">
                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
                                <rect x="10" y="20" width="100" height="80" rx="8" fill="#F5F5F5" stroke="#E0E0E0" stroke-width="2"/>
                                <path d="M10 30L60 60L110 30" stroke="#FCCA19" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="85" cy="75" r="15" fill="#FCCA19"/>
                                <path d="M85 70V80M85 75H80M85 75H90" stroke="white" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="new-email-value" class="form-label fw-semibold">{{ __('New Email Address') }}</label>
                            <input type="email" class="form-control form-control-lg" id="new-email-value" placeholder="{{ __('Enter new email') }}" style="border-radius: 8px; padding: 12px;">
                            <small class="text-danger d-block mt-1" id="new-email-error"></small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="button" class="btn btn-primary" id="verify-email-btn" style="background-color: #FCCA19; border-color: #FCCA19; color: #000; font-weight: 600;">{{ __('Verify') }}</button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Verify OTP -->
                    <div id="email-step-2" style="display: none;">
                        <h4 class="fw-bold mb-4 text-center">{{ __('Verify New Email Address') }}</h4>
                        
                        <div class="text-center mb-4">
                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
                                <rect x="10" y="20" width="100" height="80" rx="8" fill="#F5F5F5" stroke="#E0E0E0" stroke-width="2"/>
                                <path d="M10 30L60 60L110 30" stroke="#FCCA19" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="85" cy="75" r="15" fill="#FCCA19"/>
                                <path d="M78 75L83 80L92 70" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        
                        <div class="text-center mb-4">
                            <p class="mb-1" id="email-otp-sent-message">{{ __('A verification code has been sent to') }} <span id="email-otp-address" class="fw-semibold"></span>.</p>
                            <p class="text-muted small">{{ __('Please enter the code to verify your new email address.') }}</p>
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-center gap-2" id="email-otp-container">
                                <input type="text" class="email-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="email-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="email-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="email-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="email-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="email-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                            </div>
                            <small class="text-danger d-block text-center mt-2" id="email-otp-error"></small>
                        </div>
                        
                        <div class="text-center mb-3">
                            <div class="mt-1 mb-2">
                                <span class="text-muted small" id="email-resend-timer"></span>
                            </div>
                            <div id="email-resend-section" style="display: none;">
                                <p class="mb-1 small text-muted">{{ __('Haven\'t received the code?') }}</p>
                                <button type="button" class="btn btn-link p-0 text-primary text-decoration-none" id="resend-email-otp-btn" style="font-size: 14px;">{{ __('Resend code') }}</button>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="button" class="btn btn-primary" id="submit-email-otp-btn" style="background-color: #FCCA19; border-color: #FCCA19; color: #000; font-weight: 600;">{{ __('Verify') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Phone Modal -->
    <div class="modal fade" id="changePhoneModal" tabindex="-1" aria-labelledby="changePhoneModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="changePhoneModalLabel">{{ __('Change Phone') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <!-- Step 1: Enter Phone -->
                    <div id="phone-step-1">
                        <h4 class="fw-bold mb-4 text-center">{{ __('Enter New Phone Number') }}</h4>
                        
                        <div class="text-center mb-4">
                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
                                <rect x="25" y="15" width="70" height="90" rx="12" fill="#F5F5F5" stroke="#E0E0E0" stroke-width="2"/>
                                <rect x="35" y="25" width="50" height="35" rx="4" fill="#E0E0E0"/>
                                <circle cx="60" cy="75" r="8" fill="#FCCA19"/>
                                <rect x="40" y="90" width="40" height="4" rx="2" fill="#E0E0E0"/>
                            </svg>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="new-phone-value-modal" class="form-label fw-semibold">{{ __('New Phone Number') }}</label>
                            <div class="phone-area">
                                <input type="tel" class="form-control form-control-lg" id="new-phone-value-modal" name="phone" placeholder="{{ __('Enter new phone') }}" style="border-radius: 8px; padding: 12px;">
                            </div>
                            <small class="text-danger d-block mt-1" id="new-phone-error"></small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="button" class="btn btn-primary" id="verify-phone-btn" style="background-color: #FCCA19; border-color: #FCCA19; color: #000; font-weight: 600;">{{ __('Verify') }}</button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Verify OTP -->
                    <div id="phone-step-2" style="display: none;">
                        <h4 class="fw-bold mb-4 text-center">{{ __('Verify New Phone Number') }}</h4>
                        
                        <div class="text-center mb-4">
                            <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
                                <rect x="25" y="15" width="70" height="90" rx="12" fill="#F5F5F5" stroke="#E0E0E0" stroke-width="2"/>
                                <rect x="35" y="25" width="50" height="35" rx="4" fill="#E0E0E0"/>
                                <circle cx="60" cy="75" r="8" fill="#FCCA19"/>
                                <path d="M55 75L58 78L65 71" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect x="40" y="90" width="40" height="4" rx="2" fill="#E0E0E0"/>
                            </svg>
                        </div>
                        
                        <div class="text-center mb-4">
                            <p class="mb-1" id="phone-otp-sent-message">{{ __('A verification code has been sent to') }} <span id="phone-otp-address" class="fw-semibold"></span>.</p>
                            <p class="text-muted small">{{ __('Please enter the code to verify your new phone number.') }}</p>
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-center gap-2" id="phone-otp-container">
                                <input type="text" class="phone-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="phone-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="phone-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="phone-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="phone-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                                <input type="text" class="phone-otp-box form-control text-center fw-bold" maxlength="1" style="width: 50px; height: 50px; font-size: 20px; border-radius: 8px;">
                            </div>
                            <small class="text-danger d-block text-center mt-2" id="phone-otp-error"></small>
                        </div>
                        
                        <div class="text-center mb-3">
                            <div class="mt-1 mb-2">
                                <span class="text-muted small" id="phone-resend-timer"></span>
                            </div>
                            <div id="phone-resend-section" style="display: none;">
                                <p class="mb-1 small text-muted">{{ __('Haven\'t received the code?') }}</p>
                                <button type="button" class="btn btn-link p-0 text-primary text-decoration-none" id="resend-phone-otp-btn" style="font-size: 14px;">{{ __('Resend code') }}</button>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="button" class="btn btn-primary" id="submit-phone-otp-btn" style="background-color: #FCCA19; border-color: #FCCA19; color: #000; font-weight: 600;">{{ __('Verify') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        "use strict";
        var user_id = '{{ $user->id }}';
        const utilJs = "{{ asset('public/dist/js/intl-tel-input/utils.min.js') }}";
        
        // Pass Blade variables to external JS
        window.vendorProfileRoutes = {
            sendChangeOtp: '{{ route("vendor.sendChangeOtp") }}',
            verifyChangeOtp: '{{ route("vendor.verifyChangeOtp") }}',
            resendChangeOtp: '{{ route("vendor.resendChangeOtp") }}'
        };
        
        window.vendorProfileLang = {
            fieldRequired: {!! json_encode(__('This field is required.')) !!},
            validEmail: {!! json_encode(__('Enter a valid email.')) !!},
            validPhone: {!! json_encode(__('Please enter a valid phone number.')) !!},
            validOtp: {!! json_encode(__('Please enter a valid 6-digit OTP.')) !!},
            sending: {!! json_encode(__('Sending...')) !!},
            verifying: {!! json_encode(__('Verifying...')) !!},
            verify: {!! json_encode(__('Verify')) !!},
            failedToSendOtp: {!! json_encode(__('Failed to send OTP.')) !!},
            failedToVerifyOtp: {!! json_encode(__('Failed to verify OTP.')) !!},
            failedToResendOtp: {!! json_encode(__('Failed to resend OTP.')) !!},
            invalidOtp: {!! json_encode(__('Invalid OTP.')) !!},
            invalidRequest: {!! json_encode(__('Invalid request.')) !!},
            verificationSuccessful: {!! json_encode(__('Verification Successful!')) !!},
            reloadingPage: {!! json_encode(__('Reloading page...')) !!}
        };
    </script>
    
    <script src="{{ asset('public/dist/js/intl-tel-input/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/site/set-dial-code.min.js?v=5.0.0') }}"></script>

    <script src="{{ asset('public/dist/plugins/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('public/datta-able/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/user.min.js?v=3.3') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/vendor-profile-email-phone-change.min.js') }}"></script>
@endsection
