/**
 * Enhanced Auth Forms Common JavaScript
 * Handles password toggles, phone input initialization, form loading states, etc.
 */

(function() {
    'use strict';

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializePasswordToggles();
        initializePhoneInputs();
        initializeEmailPhoneToggles();
        initializeFormLoading();
        initializeOTPInput();
        initializeResendOTP();
    });

    /**
     * Initialize password toggle functionality
     */
    function initializePasswordToggles() {
        // Handle password fields with .password-field class
        const passwordFields = document.querySelectorAll('.password-field');
        passwordFields.forEach(function(passwordField) {
            const inputWrapper = passwordField.closest('.input-wrapper');
            if (inputWrapper) {
                const toggleBtn = inputWrapper.querySelector('.password-toggle-btn');
                if (toggleBtn && !toggleBtn.hasAttribute('data-toggle-initialized')) {
                    setupPasswordToggle(passwordField, toggleBtn);
                }
            }
        });

        // Handle confirm password fields
        const confirmPasswordFields = document.querySelectorAll('.password-confirm-field');
        confirmPasswordFields.forEach(function(passwordField) {
            const inputWrapper = passwordField.closest('.input-wrapper');
            if (inputWrapper) {
                const toggleBtn = inputWrapper.querySelector('.password-confirm-toggle-btn');
                if (toggleBtn && !toggleBtn.hasAttribute('data-toggle-initialized')) {
                    setupPasswordToggle(passwordField, toggleBtn);
                }
            }
        });

        // Handle specific password fields by ID (fallback)
        const specificFields = [
            { id: 'login-password', selector: '.password-toggle-btn' },
            { id: 'reset-password', selector: '.password-toggle-btn' },
            { id: 'registration-password', selector: '.password-toggle-btn' },
            { id: 'reset-password-confirm', selector: '.password-confirm-toggle-btn' },
            { id: 'registration-password-confirm', selector: '.password-confirm-toggle-btn' }
        ];

        specificFields.forEach(function(field) {
            const passwordField = document.getElementById(field.id);
            if (passwordField) {
                const inputWrapper = passwordField.closest('.input-wrapper');
                if (inputWrapper) {
                    const toggleBtn = inputWrapper.querySelector(field.selector);
                    if (toggleBtn && !toggleBtn.hasAttribute('data-toggle-initialized')) {
                        setupPasswordToggle(passwordField, toggleBtn);
                        toggleBtn.setAttribute('data-toggle-initialized', 'true');
                    }
                }
            }
        });
    }

    /**
     * Setup password toggle for a specific field
     */
    function setupPasswordToggle(passwordField, toggleBtn) {
        // Prevent duplicate initialization
        if (toggleBtn.hasAttribute('data-toggle-initialized')) {
            return;
        }

        const hideIcon = toggleBtn.querySelector('.password-hide-icon');
        const showIcon = toggleBtn.querySelector('.password-show-icon');

        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                if (hideIcon) hideIcon.style.display = 'none';
                if (showIcon) showIcon.style.display = 'block';
            } else {
                passwordField.type = 'password';
                if (hideIcon) hideIcon.style.display = 'block';
                if (showIcon) showIcon.style.display = 'none';
            }
        });

        // Mark as initialized
        toggleBtn.setAttribute('data-toggle-initialized', 'true');
    }

    /**
     * Initialize intl-tel-input for phone inputs
     */
    function initializePhoneInputs() {
        const phoneInputs = [
            document.getElementById('login-phone'),
            document.getElementById('reset-phone'),
            document.getElementById('registration-phone')
        ];

        phoneInputs.forEach(function(phoneInput) {
            if (phoneInput) {
                setTimeout(function() {
                    initializeIntlTelInput(phoneInput);
                }, 200);
            }
        });
    }

    /**
     * Function to initialize intl-tel-input
     */
    function initializeIntlTelInput(phoneInput) {
        if (!phoneInput) {
            return;
        }

        // Check if already initialized
        if (phoneInput.getAttribute('data-intl-tel-input-id')) {
            return;
        }

        // Check if intlTelInput is available
        if (typeof window.intlTelInput === 'undefined') {
            // Wait for script to load
            setTimeout(function() {
                initializeIntlTelInput(phoneInput);
            }, 100);
            return;
        }

        // Get config from window object
        const utilJs = window.ENHANCED_AUTH_FORMS_CONFIG?.utilJs || '';
        const defaultCountryCode = window.ENHANCED_AUTH_FORMS_CONFIG?.defaultCountry || '';

        try {
            window.intlTelInput(phoneInput, {
                utilsScript: utilJs,
                showSelectedDialCode: true,
                initialCountry: defaultCountryCode || '',
                separateDialCode: false,
            });

            // Add dial_code hidden input
            const form = phoneInput.closest('form');
            if (form) {
                let dialCodeInput = form.querySelector('input[name="dial_code"]');
                if (!dialCodeInput) {
                    dialCodeInput = document.createElement('input');
                    dialCodeInput.type = 'hidden';
                    dialCodeInput.name = 'dial_code';
                    form.appendChild(dialCodeInput);
                }

                // Function to update dial code
                function updateDialCode() {
                    setTimeout(function() {
                        if (window.intlTelInputGlobals && window.intlTelInputGlobals.getInstance) {
                            const iti = window.intlTelInputGlobals.getInstance(phoneInput);
                            if (iti) {
                                const dialCode = '+' + iti.getSelectedCountryData().dialCode;
                                dialCodeInput.value = dialCode;
                            }
                        }
                    }, 50);
                }

                // Update dial_code on country change
                phoneInput.addEventListener('countrychange', updateDialCode);

                // Set initial dial code
                updateDialCode();
            }
        } catch (e) {
            console.error('Error initializing intl-tel-input:', e);
        }
    }

    /**
     * Initialize email/phone toggle functionality
     */
    function initializeEmailPhoneToggles() {
        // Login form toggles
        const loginEmailSection = document.querySelector('.login-email-section');
        const loginPhoneSection = document.querySelector('.login-phone-section');
        const loginEmailInput = document.getElementById('login-email');
        const loginPhoneInput = document.getElementById('login-phone');

        // Reset form toggles
        const resetEmailSection = document.querySelector('.reset-email-section');
        const resetPhoneSection = document.querySelector('.reset-phone-section');
        const resetEmailInput = document.getElementById('reset-email');
        const resetPhoneInput = document.getElementById('reset-phone');

        document.addEventListener('click', function(e) {
            // Login form toggles
            if (e.target.classList.contains('enable-login-email-section')) {
                e.preventDefault();
                if (loginEmailSection) loginEmailSection.style.display = 'block';
                if (loginPhoneSection) loginPhoneSection.style.display = 'none';
                if (loginPhoneInput) {
                    loginPhoneInput.value = '';
                    loginPhoneInput.name = '';
                    loginPhoneInput.setAttribute('disabled', 'disabled');
                }
                if (loginEmailInput) {
                    loginEmailInput.name = 'email';
                    loginEmailInput.removeAttribute('disabled');
                }
            } else if (e.target.classList.contains('enable-login-phone-section')) {
                e.preventDefault();
                if (loginPhoneSection) loginPhoneSection.style.display = 'block';
                if (loginEmailSection) loginEmailSection.style.display = 'none';
                if (loginEmailInput) {
                    loginEmailInput.value = '';
                    loginEmailInput.name = '';
                    loginEmailInput.setAttribute('disabled', 'disabled');
                }
                if (loginPhoneInput) {
                    loginPhoneInput.name = 'phone';
                    loginPhoneInput.removeAttribute('disabled');
                    
                }
            }
            // Reset form toggles
            else if (e.target.classList.contains('enable-reset-email-section')) {
                e.preventDefault();
                if (resetEmailSection) resetEmailSection.style.display = 'block';
                if (resetPhoneSection) resetPhoneSection.style.display = 'none';
                if (resetEmailInput) {
                    resetEmailInput.name = 'email';
                    resetEmailInput.removeAttribute('disabled');
                }
                if (resetPhoneInput) {
                    resetPhoneInput.name = '';
                    resetPhoneInput.value = '';
                    resetPhoneInput.setAttribute('disabled', 'disabled');
                }
            } else if (e.target.classList.contains('enable-reset-phone-section')) {
                e.preventDefault();
                if (resetPhoneSection) resetPhoneSection.style.display = 'block';
                if (resetEmailSection) resetEmailSection.style.display = 'none';
                if (resetPhoneInput) {
                    resetPhoneInput.name = 'phone';
                    resetPhoneInput.removeAttribute('disabled');    
                }
                if (resetEmailInput) {
                    resetEmailInput.name = '';
                    resetEmailInput.value = '';
                    resetEmailInput.setAttribute('disabled', 'disabled');
                }
            }
        });
    }

    /**
     * Initialize form loading state
     */
    function initializeFormLoading() {
        const forms = document.querySelectorAll('.enhanced-login-form');
        forms.forEach(function(form) {
            const submitBtn = form.querySelector('.enhanced-submit-btn');
            if (submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.classList.add('loading');
                });
            }
        });
    }

    /**
     * Initialize OTP input specific functionality
     */
    function initializeOTPInput() {
        const otpInput = document.getElementById('otp-token');
        if (otpInput) {
            otpInput.focus();

            // Auto-format: remove non-digits
            otpInput.addEventListener('input', function(e) {
                const value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
            });

            // Only allow numeric input
            otpInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                    e.preventDefault();
                }
            });
        }
    }

    /**
     * Initialize Resend OTP functionality with timer
     */
    function initializeResendOTP() {
        const resendLink = document.querySelector('#resend-code-link');
        if (!resendLink) return;

        // Get configuration from data attributes
        const form = resendLink.closest('form');
        const resendUrl = resendLink.getAttribute('data-resend-url') || (form ? form.getAttribute('data-resend-url') : null);
        
        // Get CSRF token from form or meta tag
        let csrfToken = null;
        if (form) {
            const csrfInput = form.querySelector('input[name="_token"]');
            if (csrfInput) {
                csrfToken = csrfInput.value;
            }
        }
        if (!csrfToken) {
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                csrfToken = metaToken.getAttribute('content');
            }
        }
        
        // Get translated messages from data attributes
        const messages = {
            resendCode: document.querySelector('.resend-code').textContent || 'Resend Code',
            sending: document.querySelector('.resend-sending').textContent || 'Sending...',
            phoneRequired: resendLink.getAttribute('data-text-phone-required') || 'Phone number is required.',
            emailPhoneRequired: resendLink.getAttribute('data-text-email-phone-required') || 'Email or phone number is required.',
            success: resendLink.getAttribute('data-text-success') || 'OTP has been resent successfully.',
            error: resendLink.getAttribute('data-text-error') || 'Failed to resend OTP. Please try again.',
            resendIn: resendLink.getAttribute('data-text-resend-in') || 'Resend Code in',
            seconds: resendLink.getAttribute('data-text-seconds') || 'seconds'
        };

        const resendText = resendLink.querySelector('.resend-text');
        const messageArea = document.querySelector('#resend-otp-message');
        const otpValidityTimer = document.querySelector('#otp-validity-timer');
        const otpTimerDisplay = document.querySelector('#otp-timer-display');
        const resendCodeWrapper = document.querySelector('#resend-code-wrapper');
        const otpCreatedTimestampInput = document.querySelector('#otp-created-timestamp');
        const otpExpireInInput = document.querySelector('#otp-expire-in');
        let countdownInterval = null;
        let timeLeft = 0;
        
        // Get OTP expiration time from preference (in minutes, default 5)
        const OTP_VALIDITY_MINUTES = otpExpireInInput ? parseInt(otpExpireInInput.value) || 5 : 5;
        const OTP_VALIDITY_SECONDS = OTP_VALIDITY_MINUTES * 60;

        // Function to format time as MM:SS
        function formatTime(seconds) {
            if (seconds < 0) return '0:00';
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return minutes + ':' + (secs < 10 ? '0' : '') + secs;
        }

        // Function to calculate remaining time based on database timestamp
        function calculateRemainingTime() {
            if (!otpCreatedTimestampInput || !otpCreatedTimestampInput.value) {
                // If no timestamp available, default to preference value
                return OTP_VALIDITY_SECONDS;
            }

            const otpCreatedTimestamp = parseInt(otpCreatedTimestampInput.value);
            const currentTimestamp = Math.floor(Date.now() / 1000);
            const elapsedSeconds = currentTimestamp - otpCreatedTimestamp;
            const remainingSeconds = OTP_VALIDITY_SECONDS - elapsedSeconds;

            return Math.max(0, remainingSeconds); // Return 0 if expired
        }

        // Function to show message in blade
        function showMessage(message, type) {
            if (!messageArea) return;
            
            messageArea.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
            messageArea.innerHTML = '<strong>' + message + '</strong>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            messageArea.classList.remove('d-none');
            
            // Auto hide after 5 seconds
            setTimeout(function() {
                messageArea.classList.add('d-none');
            }, 5000);
        }

        // Function to start OTP validity countdown (based on preference and database timestamp)
        function startOtpValidityTimer() {
            // Clear any existing interval
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            // Calculate remaining time based on database timestamp
            timeLeft = calculateRemainingTime();
            
            if (timeLeft > 0) {
                // OTP is still valid - show timer
                if (otpValidityTimer) otpValidityTimer.classList.remove('d-none');
                if (resendCodeWrapper) resendCodeWrapper.classList.add('d-none');
                if (otpTimerDisplay) otpTimerDisplay.textContent = formatTime(timeLeft);
                resendLink.style.pointerEvents = 'none';
                
                countdownInterval = setInterval(function() {
                    timeLeft--;
                    if (otpTimerDisplay) otpTimerDisplay.textContent = formatTime(timeLeft);
                    
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        countdownInterval = null;
                        
                        // Hide OTP validity timer, show resend code wrapper
                        if (otpValidityTimer) otpValidityTimer.classList.add('d-none');
                        if (resendCodeWrapper) resendCodeWrapper.classList.remove('d-none');
                        resendLink.style.pointerEvents = 'auto';
                        // Reset resend text to "Resend Code" when timer expires
                        if (resendText) resendText.textContent = messages.resendCode;
                    }
                }, 1000);
            } else {
                // OTP has already expired - show resend option
                if (otpValidityTimer) otpValidityTimer.classList.add('d-none');
                if (resendCodeWrapper) resendCodeWrapper.classList.remove('d-none');
                resendLink.style.pointerEvents = 'auto';
                // Reset resend text to "Resend Code" when OTP is already expired
                if (resendText) resendText.textContent = messages.resendCode;
            }
        }

        // Start countdown on page load
        startOtpValidityTimer();

        // Handle resend click
        resendLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Prevent multiple clicks during countdown
            if (resendLink.style.pointerEvents === 'none') {
                return;
            }

            // Check for email or phone (for password reset, it can be either)
            const phone = document.querySelector('input[name="phone"]');
            const email = document.querySelector('input[name="email"]');
            const phoneValue = phone ? phone.value : '';
            const emailValue = email ? email.value : '';
            
            // Determine which field is required based on the message key
            const isPhoneRequired = messages.phoneRequired || messages.emailPhoneRequired;
            const requiredMessage = messages.emailPhoneRequired || messages.phoneRequired || 'Email or phone number is required.';
            
            if (!phoneValue && !emailValue) {
                showMessage(requiredMessage, 'error');
                return;
            }

            if (!resendUrl || !csrfToken) {
                showMessage(messages.error, 'error');
                console.error('Resend OTP: Missing URL or CSRF token');
                return;
            }

            // Disable link immediately
            resendLink.style.pointerEvents = 'none';
            if (resendText) resendText.textContent = messages.sending;

            // Prepare request body with email or phone
            const requestBody = {};
            if (emailValue) {
                requestBody.email = emailValue;
            }
            if (phoneValue) {
                requestBody.phone = phoneValue;
            }

            fetch(resendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(requestBody)
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.status === 'success') {
                    showMessage(data.message || messages.success, 'success');
                    
                    // Update the OTP creation timestamp from server response or use current time
                    if (otpCreatedTimestampInput) {
                        const timestamp = data.otp_created_at || Math.floor(Date.now() / 1000);
                        otpCreatedTimestampInput.value = timestamp;
                    }
                    
                    // Restart 5-minute OTP validity timer
                    startOtpValidityTimer();
                } else {
                    showMessage(data.message || messages.error, 'error');
                    // Re-enable link on error
                    resendLink.style.pointerEvents = 'auto';
                    if (resendText) resendText.textContent = messages.resendCode;
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showMessage(messages.error, 'error');
                // Re-enable link on error
                resendLink.style.pointerEvents = 'auto';
                if (resendText) resendText.textContent = messages.resendCode;
            });
        });
    }
})();

