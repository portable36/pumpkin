@extends('admin.auth.login_templates.' . (isset($template) ? $template : preference('auth_template_name', 'template-1')) . '.index')

@section('sub-content')
<link rel="stylesheet" href="{{ asset('public/dist/css/auth/enhanced-auth-forms.min.css') }}">
<form method="GET" action="{{ route('password.reset', ['token' => 'tokens']) }}" class="admin-login-con my-0 enhanced-login-form" id="admin-otp-form">
    @csrf
    
    <h3 class="login-title">{{ __("OTP Verification") }}</h3>
    
    <p class="login-box-msg mb-3">{{ __('Enter the OTP code sent to your email or phone') }}</p>
    
    <div class="notification-wrapper mb-3">
        @include('admin.auth.partial.notification')
        {{-- Dynamic message area for resend OTP --}}
        <div id="resend-otp-message" class="d-none"></div>
    </div>

    {{-- OTP Input Section --}}
    <div class="form-group-enhanced mb-4">
        <div class="input-wrapper position-relative">
            <div class="input-icon-wrapper">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <rect x="2" y="7" width="20" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="7" cy="12" r="1" fill="currentColor"/>
                    <circle cx="12" cy="12" r="1" fill="currentColor"/>
                    <circle cx="17" cy="12" r="1" fill="currentColor"/>
                </svg>
            </div>
            <input id="otp-token" type="text" class="form-control enhanced-input py-2" name="token" value="" placeholder="{{ __('Enter OTP') }}" autocomplete="one-time-code" maxlength="4">
        </div>
    </div>

    {{-- Hidden fields for email/phone --}}
    <input type="hidden" name="email" id="reset-email" value="{{ old('email', $email ?? '') }}">
    <input type="hidden" name="phone" id="reset-phone" value="{{ old('phone', $phone ?? '') }}">

    <div class="text-center mb-3 bg-gray p-10p">
        <div class="otp-validity-timer mb-2" id="otp-validity-timer">
            <span class="mb-0 text-muted" style="font-size: 13px;">{{ __('OTP is valid for') }}:</span>
            <span class="mb-0 d-inline-block mt-1" style="font-size: 16px; font-weight: 600; color: #FCCA19;">
                <span id="otp-timer-display">{{ sprintf('%d:%02d', $otp_expire_in ?? 5, 0) }}</span>
            </span>
        </div>
        @if(isset($otpCreatedAt))
            <input type="hidden" id="otp-created-timestamp" value="{{ $otpCreatedAt }}">
        @endif
        @if(isset($otpExpireIn))
            <input type="hidden" id="otp-expire-in" value="{{ $otpExpireIn }}">
        @else
            <input type="hidden" id="otp-expire-in" value="5">
        @endif
        <div class="resend-code-wrapper d-none" id="resend-code-wrapper">
            <p class="mb-2 text-muted">{{ __("OTP has expired. Didn't receive the code?") }}</p>
            <a class="text-muted register-link resend-verification-code-password" 
               href="javascript:void(0)" 
               id="resend-code-link"
               data-resend-url="{{ route('password.resendOtp') }}"
               data-text-resend="{{ __('Resend Code') }}"
               data-text-sending="{{ __('Sending...') }}"
               data-text-email-phone-required="{{ __('Email or phone number is required.') }}"
               data-text-success="{{ __('OTP has been resent successfully.') }}"
               data-text-error="{{ __('Failed to resend OTP. Please try again.') }}"
               data-text-otp-valid="{{ __('OTP is valid for') }}"
               data-text-minutes="{{ __('minutes') }}"
               data-text-seconds="{{ __('seconds') }}">
               <span class="resend-code d-none">{{ __('Resend Code') }}</span>
               <span class="resend-sending d-none">{{ __('Sending') }}...</span>
                <span class="resend-text">{{ __('Resend Code') }}</span>
            </a>
        </div>
    </div>

    <button class="btn btn-mv-primary enhanced-submit-btn mb-4 ltr:me-1 rtl:ms-1 loader w-100" type="submit">
        <span class="btn-text">{{ __("Continue") }}</span>
        <svg role="status" class="anim spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
        </svg>
    </button>
    
    <p class="mb-2 text-muted text-center">{{ __("Click here to") }} <a class="text-muted register-link" href="{{ route('login') }}">{{ __("Log In") }}</a></p>
</form>

<script src="{{ asset('public/dist/js/custom/auth/enhanced-auth-forms.min.js?v=1.1.0') }}"></script>

@endsection
