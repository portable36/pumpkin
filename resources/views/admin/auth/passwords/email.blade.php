@extends('admin.auth.login_templates.' . (isset($template) ? $template : preference('auth_template_name', 'template-1')) . '.index')

@section('sub-content')
<link rel="stylesheet" href="{{ asset('public/dist/css/intl-tel-input/intlTelInput.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/dist/css/auth/enhanced-auth-forms.min.css') }}">
<form method="POST" action="{{ route('login.sendResetLink') }}" class="admin-login-con my-0 enhanced-login-form" id="admin-reset-password-form">
    @csrf
    
    <h3 class="login-title">{{ __("Reset Your Password") }}</h3>
    
    @if(is_null(session('success')))
        <p class="login-box-msg mb-3">{{ __('Enter your email or phone to send password reset link') }}</p>
    @endif
    
    @include('admin.auth.partial.notification')

    {{-- Email Input Section --}}
    <div class="form-group-enhanced mb-4 reset-email-section" style="display: {{ preference('user_login') == 'phone' ? 'none' : 'block' }};">
        <div class="input-wrapper position-relative">
            <div class="input-icon-wrapper">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M15.75 3.75H2.25C1.42157 3.75 0.75 4.42157 0.75 5.25V12.75C0.75 13.5784 1.42157 14.25 2.25 14.25H15.75C16.5784 14.25 17.25 13.5784 17.25 12.75V5.25C17.25 4.42157 16.5784 3.75 15.75 3.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M0.75 6L9 10.5L17.25 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <input id="reset-email" type="email" class="form-control enhanced-input py-2" value="{{ old('email') }}" name="email" placeholder="{{ __('Email') }}" autocomplete="email" {{ preference('user_login') == 'phone' ? 'disabled' : '' }}>
            @if (preference('user_login', 'both') == 'both')
                <span class="enable-reset-phone-section toggle-link">{{ __('Use Phone Instead') }}</span>
            @endif
        </div>
    </div>

    {{-- Phone Input Section --}}
    <div class="form-group-enhanced mb-4 reset-phone-section" style="display: {{ preference('user_login') == 'phone' ? 'block' : 'none' }};">
        <div class="input-wrapper position-relative">
            <input id="reset-phone" type="text" class="form-control enhanced-input py-2 cc-phone" name="phone" placeholder="{{ __('Phone Number') }}" autocomplete="tel" {{ preference('user_login') != 'phone' ? 'disabled' : '' }}>
            @if (preference('user_login', 'both') == 'both')
                <span class="enable-reset-email-section toggle-link">{{ __('Use Email Instead') }}</span>
            @endif
        </div>
    </div>
    
    @include('admin.auth.partial.re-captcha')

    <button class="btn btn-mv-primary enhanced-submit-btn mb-4 ltr:me-1 rtl:ms-1 loader w-100" type="submit">
        <span class="btn-text">{{ __("Send") }}</span>
        <svg role="status" class="anim spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
        </svg>
    </button>
    
    <p class="mb-2 text-muted text-center">{{ __("Click here to") }} <a class="text-muted register-link" href="{{ route('login') }}">{{ __("Log In") }}</a></p>
</form>

<script>
    window.ENHANCED_AUTH_FORMS_CONFIG = {
        utilJs: "{{ asset('public/dist/js/intl-tel-input/utils.min.js') }}",
        defaultCountry: "{{ preference('default_country_code', '') ?: '' }}"
    };
</script>
<script src="{{ asset('public/dist/js/intl-tel-input/intlTelInput.min.js') }}"></script>
<script src="{{ asset('public/dist/js/custom/auth/enhanced-auth-forms.min.js') }}"></script>

@endsection
