@extends('delivery::layouts.app')
@section('page_title', __('Carrier Profile'))
@section('css')
    <link rel="stylesheet" href="{{ asset('public/datta-able/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dist/plugins/lightbox/css/lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/MediaManager/Resources/assets/css/media-manager.min.css') }}">
@endsection

@section('content')
    <!-- Main content -->
    <div class="col-sm-12" id="user-edit-container">
        <div class="card">
            <div class="card-header">
                <h5><a href="{{ route('carrier.dashboard') }}">{{ __('Dashboard') }}</a> >> {{ $user->name }} >>
                    {{ __('Profile') }}</h5>
            </div>
            <div class="card-body px-4" id="no_shadow_on_card">
                <div class="col-sm-12 m-t-20 m-b-15 form-tabs">
                    <ul class="nav nav-tabs " id="myTab" role="tablist">
                        <li class="nav-item" id="kk">
                            <a class="nav-link active text-uppercase fragment-url font-bold" id="user-info-tab"
                                data-bs-toggle="tab" href="#user-info" role="tab" aria-controls="user-info"
                                aria-selected="true">{{ __(':x Information', ['x' => __('Delivery Boy')]) }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase fragment-url font-bold" id="password-tab" data-bs-toggle="tab"
                                data-rel="{{ $user->id }}" href="#password" role="tab" aria-controls="password"
                                aria-selected="false">{{ __('Update Password') }}</a>
                        </li>
                    </ul>
                    <div class="col-sm-12 tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="user-info" role="tabpanel"
                            aria-labelledby="user-info-tab">
                            <form action='{{ route('carrier.profile_update', ['id' => Auth::user()->id]) }}' method="post"
                                class="form-horizontal" id="userEdit" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group row">
                                            <label for="first_name" class="col-sm-2 col-form-label require pr-0">
                                                {{ __('Name') }}
                                            </label>
                                            <div class="col-sm-10">
                                                <input type="text" placeholder="{{ __('Name') }}"
                                                    class="form-control inputFieldDesign" id="name" name="name"
                                                    required minlength="3"
                                                    value="{{ !empty(old('name')) ? old('name') : $user->name }}"
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Name'), 'y' => 3]) }}">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="email"
                                                class="col-sm-2 col-form-label require">{{ __('Email') }}</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control inputFieldDesign" id="email"
                                                    name="email"
                                                    value="{{ !empty(old('email')) ? old('email') : $user->email }}"
                                                    placeholder="{{ __('Email') }}" required
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                    data-type-mismatch="{{ __('Enter a valid :x.', ['x' => strtolower(__('Email'))]) }}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="phone"
                                                class="col-sm-2 col-form-label require">{{ __('Phone') }}</label>
                                            <div class="col-sm-10">
                                                <input type="tel" placeholder="{{ __('Phone') }}"
                                                    class="form-control phone-number inputFieldDesign"
                                                    id="phone" name="phone"
                                                    value="{{ $user->phone }}" maxlength="45"
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                    required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="address"
                                                class="col-sm-2 col-form-label require">{{ __('Address') }}</label>
                                            <div class="col-sm-10">
                                                <textarea placeholder="{{ __('Address') }}" id="address" class="form-control" name="address" minlength="5"
                                                    data-min-length="{{ __('Address should be at least 5 characters.') }}" maxlength="191"
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')" required>{{ $user->address }}</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row d-none">
                                            <label for="role_id"
                                                class="col-sm-2 control-label">{{ __('Role') }}</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" disabled name="role_ids[]"
                                                    id="role_id">
                                                    @foreach ($roles as $key => $role)
                                                        <option value="{{ $role->id }}"
                                                            {{ in_array($role->id, $roleIds) ? 'selected' : '' }}>
                                                            {{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row d-none">
                                            <label for="Status"
                                                class="col-sm-2 col-form-label require">{{ __('Status') }}</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" disabled name="status" id="status">
                                                    <option value="Pending"
                                                        {{ old('status', $user->status) == 'Pending' ? 'selected' : '' }}>
                                                        {{ __('Pending') }}</option>
                                                    <option value="Active"
                                                        {{ old('status', $user->status) == 'Active' ? 'selected' : '' }}>
                                                        {{ __('Active') }}</option>
                                                    <option value="Inactive"
                                                        {{ old('status', $user->status) == 'Inactive' ? 'selected' : '' }}>
                                                        {{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row conditional preview-parent">
                                            <label class="col-sm-2 control-label">{{ __('Picture') }}</label>
                                            <div class="col-sm-10">
                                                <div class="custom-file position-relative media-manager-img"
                                                    data-val="single" data-returntype="ids" id="image-status"
                                                    data-type="{{ implode(',', getFileExtensions(3)) }}">
                                                    <input class="custom-file-input is-image form-control" accept="{{ implode(',', getFileExtensions(3)) }}" name="custom_file_input">
                                                    <label class="custom-file-label overflow_hidden"
                                                        for="validatedCustomFile">{{ __('Upload Image') }}</label>
                                                </div>
                                                <div class="preview-image" id="#">
                                                    <!-- img will be shown here -->
                                                    <div class="d-flex flex-wrap mt-2">
                                                        <div
                                                            class="position-relative border boder-1 p-1 mr-2 rounded mt-2">
                                                            <img class="upl-img p-1 neg-transition-scale"
                                                                src="{{ $user->fileUrl() }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divNote">
                                            <label class="col-sm-2 control-label"></label>
                                            <div class="col-sm-10" id='note_txt_1'>
                                                <span class="badge badge-danger">{{ __('Note') }}!</span>
                                                {{ implode(',', getFileExtensions(3)) }}
                                            </div>
                                            <div class="col-md-9" id='note_txt_2'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-10 px-0 m-l-5">
                                    <a href="{{ route('carrier.dashboard') }}"
                                        class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
                                    <button class="btn custom-btn-submit" type="submit"
                                        id="btnSubmit">{{ __('Save') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <div class="row">
                                <div class="col-sm-12">
                                    <form action='{{ route('carrier.password_update', ['id' => Auth::user()->id]) }}'
                                        class="form-horizontal" id="password-form" method="POST">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="old_password"
                                                class="col-sm-2 text-left col-form-label require">{{ __('Old Password') }}</label>
                                            <div class="col-sm-6">
                                                <input type="password" class="form-control inputFieldDesign"
                                                    id="old_password" name="old_password"
                                                    placeholder="{{ __('Old Password') }}"
                                                    required minlength="5"
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Old Password'), 'y' => 5]) }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                class="col-sm-2 text-left col-form-label require">{{ __('New Password') }}</label>
                                            <div class="col-sm-6">
                                                <input type="password" class="form-control inputFieldDesign"
                                                    id="password" name="password" placeholder="{{ __('New Password') }}"
                                                    value="{{ old('password') }}" required minlength="5"
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('New Password'), 'y' => 5]) }}">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-1">
                                            <label for="password"
                                                class="col-sm-2 text-left col-form-label require">{{ __('Confirm Password') }}</label>
                                            <div class="col-sm-6">
                                                <input type="password" class="form-control inputFieldDesign"
                                                    id="confirm_password" name="password_confirmation"
                                                    placeholder="{{ __('Confirm Password') }}" required minlength="5"
                                                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                            </div>
                                        </div>
                                        <div class="col-sm-10 px-0 m-l-5">
                                            <a href="{{ route('carrier.dashboard') }}"
                                                class="btn custom-btn-cancel all-cancel-btn">{{ __('Cancel') }}</a>
                                            <button class="btn custom-btn-submit" type="submit"
                                                id="btnSubmit1">{{ __('Save') }}</button>
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
    @include('mediamanager::image.modal_image')
@endsection

@section('js')
    <script type="text/javascript">
        "use strict";
        var user_id = '{{ $user->id }}';
        var vendorStaffRole = null;
    </script>

    <script src="{{ asset('public/dist/plugins/lightbox/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('public/datta-able/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/user.min.js') }}"></script>
    <script src="{{ asset('public/dist/js/custom/validation.min.js') }}"></script>
@endsection
