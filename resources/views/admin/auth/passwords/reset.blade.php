@extends('admin.auth.login_templates.' . (isset($template) ? $template : preference('auth_template_name', 'template-1')) . '.index')

@section('sub-content')
<link rel="stylesheet" href="{{ asset('public/dist/css/auth/enhanced-auth-forms.min.css') }}">
<form method="POST" action="{{ route('password.resets') }}" class="admin-login-con my-0 enhanced-login-form" id="admin-reset-password-form">
    @csrf
    <input type="hidden" name="id" value="{{ $user->id }}">
    <input type="hidden" name="token" value="{{ $token }}">
    
    <h3 class="login-title">{{ __("Reset Password") }}</h3>
    
    <p class="login-box-msg mb-3">{{ __('Enter your new password below') }}</p>
    
    <div class="notification-wrapper mb-3">
        @include('admin.auth.partial.notification')
    </div>
    
    {{-- Password Input Section --}}
    <div class="form-group-enhanced mb-4">
        <div class="input-wrapper position-relative">
            <div class="input-icon-wrapper">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M13.5 6.75V4.5C13.5 2.51088 11.7391 0.75 9.75 0.75H8.25C6.26088 0.75 4.5 2.51088 4.5 4.5V6.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="2.25" y="6.75" width="13.5" height="9.75" rx="1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 12.75V12.7575" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <input id="reset-password" type="password" class="form-control enhanced-input py-2 password-field" name="password" placeholder="{{ __('Password') }}" autocomplete="new-password" required>
            <button type="button" class="password-toggle-btn" aria-label="{{ __('Toggle password visibility') }}">
                <svg class="password-hide-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" style="display: block;">
                    <path d="M1 1L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M9 3C5 3 1.9 6.14 1 9C1.9 11.86 5 15 9 15C13 15 16.1 11.86 17 9C16.1 6.14 13 3 9 3Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12.5 12.5C11.4558 13.2161 10.2687 13.5927 9.0415 13.5779C7.8143 13.5632 6.63407 13.1589 5.62094 12.4154C4.60781 11.6719 3.80272 10.6198 3.31439 9.4001C2.82607 8.18045 2.67798 6.84949 2.89014 5.56349"/>
                    <path d="M10.59 10.59C10.2142 10.856 9.74454 11.0019 9.25581 10.9908C8.76709 10.9797 8.30504 10.8128 7.95286 10.5241C7.60068 10.2355 7.38432 9.84599 7.34911 9.42378C7.3139 9.00156 7.46408 8.58948 7.76219 8.28774C8.06031 7.986 8.4873 7.82301 8.92747 7.84419C9.36764 7.86536 9.7763 8.0678 10.05 8.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <svg class="password-show-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" style="display: none;">
                    <path d="M9 3.75C5.25 3.75 2.025 5.7 0.3 8.55L0 9L0.3 9.45C2.025 12.3 5.25 14.25 9 14.25C12.75 14.25 15.975 12.3 17.7 9.45L18 9L17.7 8.55C15.975 5.7 12.75 3.75 9 3.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 11.25C10.2426 11.25 11.25 10.2426 11.25 9C11.25 7.75736 10.2426 6.75 9 6.75C7.75736 6.75 6.75 7.75736 6.75 9C6.75 10.2426 7.75736 11.25 9 11.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Confirm Password Input Section --}}
    <div class="form-group-enhanced mb-4">
        <div class="input-wrapper position-relative">
            <div class="input-icon-wrapper">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M13.5 6.75V4.5C13.5 2.51088 11.7391 0.75 9.75 0.75H8.25C6.26088 0.75 4.5 2.51088 4.5 4.5V6.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="2.25" y="6.75" width="13.5" height="9.75" rx="1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 12.75V12.7575" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <input id="reset-password-confirm" type="password" class="form-control enhanced-input py-2 password-confirm-field" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" autocomplete="new-password" required>
            <button type="button" class="password-toggle-btn password-confirm-toggle-btn" aria-label="{{ __('Toggle password visibility') }}">
                <svg class="password-hide-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" style="display: block;">
                    <path d="M1 1L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M9 3C5 3 1.9 6.14 1 9C1.9 11.86 5 15 9 15C13 15 16.1 11.86 17 9C16.1 6.14 13 3 9 3Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12.5 12.5C11.4558 13.2161 10.2687 13.5927 9.0415 13.5779C7.8143 13.5632 6.63407 13.1589 5.62094 12.4154C4.60781 11.6719 3.80272 10.6198 3.31439 9.4001C2.82607 8.18045 2.67798 6.84949 2.89014 5.56349"/>
                    <path d="M10.59 10.59C10.2142 10.856 9.74454 11.0019 9.25581 10.9908C8.76709 10.9797 8.30504 10.8128 7.95286 10.5241C7.60068 10.2355 7.38432 9.84599 7.34911 9.42378C7.3139 9.00156 7.46408 8.58948 7.76219 8.28774C8.06031 7.986 8.4873 7.82301 8.92747 7.84419C9.36764 7.86536 9.7763 8.0678 10.05 8.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <svg class="password-show-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" style="display: none;">
                    <path d="M9 3.75C5.25 3.75 2.025 5.7 0.3 8.55L0 9L0.3 9.45C2.025 12.3 5.25 14.25 9 14.25C12.75 14.25 15.975 12.3 17.7 9.45L18 9L17.7 8.55C15.975 5.7 12.75 3.75 9 3.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 11.25C10.2426 11.25 11.25 10.2426 11.25 9C11.25 7.75736 10.2426 6.75 9 6.75C7.75736 6.75 6.75 7.75736 6.75 9C6.75 10.2426 7.75736 11.25 9 11.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <button class="btn btn-mv-primary enhanced-submit-btn mb-4 ltr:me-1 rtl:ms-1 loader w-100" type="submit">
        <span class="btn-text">{{ __("Reset Password") }}</span>
        <svg role="status" class="anim spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"></path>
        </svg>
    </button>
    
    <p class="mb-2 text-muted text-center">{{ __("Click here to") }} <a class="text-muted register-link" href="{{ route('login') }}">{{ __("Log In") }}</a></p>
</form>

<script src="{{ asset('public/dist/js/custom/auth/enhanced-auth-forms.min.js') }}"></script>

@endsection
