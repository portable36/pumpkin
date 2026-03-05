@extends('admin.auth.login_templates.' . (isset($template) ? $template : preference('auth_template_name', 'template-1')) . '.index')

@section('sub-content')
<link rel="stylesheet" href="{{ asset('public/dist/css/intl-tel-input/intlTelInput.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/dist/css/auth/enhanced-auth-forms.min.css') }}">
<form action="{{ route('login.post') }}" method="post" class="admin-login-con my-0 enhanced-login-form" id="admin-login-form">
    @csrf
    
    <h3 class="login-title">{{ __("Login") }}</h3>
                            
    <div class="notification-wrapper mb-3">
        @include('admin.auth.partial.notification')
    </div>

    {{-- Email Input Section --}}
    @php
        $saasActive = isActive('SaaS');
        // In SaaS, phone is shown first
        $isShowPhone = preference('user_login', 'both') == 'phone' || preference('user_login', 'both') == 'both';
        $isShowEmail = preference('user_login', 'both') == 'email' || preference('user_login', 'both') == 'both';
        if ($saasActive) {
            $showPhoneFirst = true;
        } else {
            $showPhoneFirst = preference('user_login', 'both') == 'phone';
        }
    @endphp

    {{-- Phone Input Section --}}
    @if ($isShowPhone)
        <div class="form-group-enhanced mb-4 login-phone-section" style="display: {{ ($isShowPhone && $isShowEmail) ? ($showPhoneFirst ? 'block' : 'none') : 'block' }};">
            <div class="input-wrapper position-relative">
                <input id="login-phone" type="text" class="form-control enhanced-input py-2 cc-phone" name="phone" placeholder="{{ __('Phone Number') }}" autocomplete="tel" {{ $isShowPhone && $showPhoneFirst ? '' : 'disabled' }}>
                @if ($isShowPhone && $isShowEmail)
                    <span class="enable-login-email-section toggle-link">{{ __('Use Email Instead') }}</span>
                @endif
            </div>
        </div>
    @endif

    {{-- Email Input Section --}}
    @if ($isShowEmail)
        <div class="form-group-enhanced mb-4 login-email-section" style="display: {{ ($isShowPhone && $isShowEmail) ? ($showPhoneFirst ? 'none' : 'block') : 'block' }};">
            <div class="input-wrapper position-relative">
                <div class="input-icon-wrapper">
                    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M15.75 3.75H2.25C1.42157 3.75 0.75 4.42157 0.75 5.25V12.75C0.75 13.5784 1.42157 14.25 2.25 14.25H15.75C16.5784 14.25 17.25 13.5784 17.25 12.75V5.25C17.25 4.42157 16.5784 3.75 15.75 3.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M0.75 6L9 10.5L17.25 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <input id="login-email" type="email" class="form-control enhanced-input py-2" value="{{ old('email') }}" name="email" placeholder="{{ __('Email') }}" autocomplete="email" {{ $isShowEmail && !$showPhoneFirst ? '' : 'disabled' }}>
                @if ($isShowPhone && $isShowEmail)
                    <span class="enable-login-phone-section toggle-link">{{ __('Use Phone Instead') }}</span>
                @endif
            </div>
        </div>
    @endif
    
    <div class="form-group-enhanced mb-4">
        <div class="input-wrapper position-relative">
            <div class="input-icon-wrapper">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M13.5 6.75V4.5C13.5 2.51088 11.7391 0.75 9.75 0.75H8.25C6.26088 0.75 4.5 2.51088 4.5 4.5V6.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="2.25" y="6.75" width="13.5" height="9.75" rx="1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 12.75V12.7575" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <input id="login-password" type="password" class="form-control enhanced-input py-2 password-field" name="password" placeholder="{{ __('Password') }}" autocomplete="current-password">
            <button type="button" class="password-toggle-btn" aria-label="{{ __('Toggle password visibility') }}">
                <svg class="password-hide-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 128 128" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m79.891 65.078 7.27-7.27C87.69 59.787 88 61.856 88 64c0 13.234-10.766 24-24 24-2.144 0-4.213-.31-6.192-.839l7.27-7.27a15.929 15.929 0 0 0 14.813-14.813zm47.605-3.021c-.492-.885-7.47-13.112-21.11-23.474l-5.821 5.821c9.946 7.313 16.248 15.842 18.729 19.602C114.553 71.225 95.955 96 64 96c-4.792 0-9.248-.613-13.441-1.591l-6.573 6.573C50.029 102.835 56.671 104 64 104c41.873 0 62.633-36.504 63.496-38.057a3.997 3.997 0 0 0 0-3.886zm-16.668-39.229-88 88C22.047 111.609 21.023 112 20 112s-2.047-.391-2.828-1.172a3.997 3.997 0 0 1 0-5.656l11.196-11.196C10.268 83.049 1.071 66.964.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24c10.827 0 20.205 2.47 28.222 6.122l12.95-12.95c1.563-1.563 4.094-1.563 5.656 0s1.563 4.094 0 5.656zM34.333 88.011 44.46 77.884C41.663 73.96 40 69.175 40 64c0-13.234 10.766-24 24-24 5.175 0 9.96 1.663 13.884 4.459l8.189-8.189C79.603 33.679 72.251 32 64 32 32.045 32 13.447 56.775 8.707 63.994c3.01 4.562 11.662 16.11 25.626 24.017zm15.934-15.935 21.809-21.809C69.697 48.862 66.958 48 64 48c-8.822 0-16 7.178-16 16 0 2.958.862 5.697 2.267 8.076z" fill="currentColor" opacity="1" data-original="currentColor" class=""></path></g></svg>
                <svg class="password-show-icon" style="display: none" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" x="0" y="0" viewBox="0 0 511.999 511.999" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M508.745 246.041c-4.574-6.257-113.557-153.206-252.748-153.206S7.818 239.784 3.249 246.035a16.896 16.896 0 0 0 0 19.923c4.569 6.257 113.557 153.206 252.748 153.206s248.174-146.95 252.748-153.201a16.875 16.875 0 0 0 0-19.922zM255.997 385.406c-102.529 0-191.33-97.533-217.617-129.418 26.253-31.913 114.868-129.395 217.617-129.395 102.524 0 191.319 97.516 217.617 129.418-26.253 31.912-114.868 129.395-217.617 129.395z" fill="currentColor" opacity="1" data-original="currentColor" class=""></path><path d="M255.997 154.725c-55.842 0-101.275 45.433-101.275 101.275s45.433 101.275 101.275 101.275S357.272 311.842 357.272 256s-45.433-101.275-101.275-101.275zm0 168.791c-37.23 0-67.516-30.287-67.516-67.516s30.287-67.516 67.516-67.516 67.516 30.287 67.516 67.516-30.286 67.516-67.516 67.516z" fill="currentColor" opacity="1" data-original="currentColor" class=""></path></g></svg>
            </button>

        </div>
    </div>
    
    <div class="form-group-enhanced mb-4 d-flex justify-content-between align-items-center">
        <div class="checkbox-wrapper">
            <label for="checkbox-fill-a1" class="custom-checkbox-label">
                <input type="checkbox" name="remember" id="checkbox-fill-a1" class="custom-checkbox-input" checked>
                <span class="checkbox-text">{{ __('Remember Me') }}</span>
            </label>
        </div>
        <div>
            <a href="{{ route('login.reset') }}" class="forgot-password-link">{{ __('Forgot password?') }}</a>
        </div>
    </div>

    @include('admin.auth.partial.re-captcha')
    
    <button class="btn btn-mv-primary enhanced-submit-btn mb-4 ltr:me-1 rtl:ms-1 loader w-100" type="submit">
        <span class="btn-text">{{ __("Login") }}</span>
        <svg role="status" class="anim spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
        </svg>
    </button>
    
    @if (isActive('SaaS'))
        <p class="mb-2 text-muted text-center">{{ __("Don't have an account?") }} <a class="text-muted register-link" href="{{ route('saas.registration') }}">{{ __("Sign Up") }}</a></p>
    @endif
    @if ((App\Facades\Theme::current()?->theme['name'] ?? '') == 'minimal')
        <p class="mb-2 text-muted text-center">{{ __("New to :x?", ['x' => preference('company_name')]) }} <a class="text-muted register-link" href="{{ route('registration') }}">{{ __("Join now") }}</a></p>
    @endif

    @include('admin.auth.partial.demo-credential')
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
