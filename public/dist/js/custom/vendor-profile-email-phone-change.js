(function($) {
    "use strict";
    
    let emailTempOtpId = null;
    let phoneTempOtpId = null;
    let emailResendTimer = null;
    let phoneResendTimer = null;
    let emailTimerSeconds = 60;
    let phoneTimerSeconds = 60;
    
    // Helper function to get OTP from boxes
    function getOtpFromBoxes(containerSelector) {
        let otp = '';
        $(containerSelector + ' .email-otp-box, ' + containerSelector + ' .phone-otp-box').each(function() {
            otp += $(this).val();
        });
        return otp;
    }
    
    // Helper function to clear OTP boxes
    function clearOtpBoxes(containerSelector) {
        $(containerSelector + ' .email-otp-box, ' + containerSelector + ' .phone-otp-box').val('');
    }
    
    // Helper function to handle OTP box navigation
    function setupOtpBoxes(containerSelector) {
        $(containerSelector + ' .email-otp-box, ' + containerSelector + ' .phone-otp-box').on('input', function() {
            const value = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(value);
            
            if (value && $(this).next('.email-otp-box, .phone-otp-box').length) {
                $(this).next('.email-otp-box, .phone-otp-box').focus();
            } else if (value && !$(this).next('.email-otp-box, .phone-otp-box').length) {
                // If last box is filled, check if all boxes are filled and auto-submit
                const otp = getOtpFromBoxes(containerSelector);
                if (otp.length === 6) {
                    // Auto-submit after a short delay
                    setTimeout(function() {
                        if (containerSelector === '#email-otp-container') {
                            $('#submit-email-otp-btn').click();
                        } else {
                            $('#submit-phone-otp-btn').click();
                        }
                    }, 100);
                }
            }
        }).on('keydown', function(e) {
            if (e.key === 'Backspace' && !$(this).val() && $(this).prev('.email-otp-box, .phone-otp-box').length) {
                $(this).prev('.email-otp-box, .phone-otp-box').focus();
            } else if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                const otp = getOtpFromBoxes(containerSelector);
                if (otp.length === 6) {
                    if (containerSelector === '#email-otp-container') {
                        $('#submit-email-otp-btn').click();
                    } else {
                        $('#submit-phone-otp-btn').click();
                    }
                }
            }
        }).on('paste', function(e) {
            e.preventDefault();
            const paste = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/[^0-9]/g, '').substring(0, 6);
            const boxes = $(containerSelector + ' .email-otp-box, ' + containerSelector + ' .phone-otp-box');
            digits.split('').forEach((digit, index) => {
                if (boxes.eq(index).length) {
                    boxes.eq(index).val(digit);
                }
            });
            if (digits.length === 6) {
                boxes.last().focus();
                // Auto-submit after paste
                setTimeout(function() {
                    if (containerSelector === '#email-otp-container') {
                        $('#submit-email-otp-btn').click();
                    } else {
                        $('#submit-phone-otp-btn').click();
                    }
                }, 100);
            }
        });
    }
    
    // Timer function - counts down until OTP expires, then shows resend option
    function startResendTimer(type, expireInMinutes) {
        const timerSelector = type === 'email' ? '#email-resend-timer' : '#phone-resend-timer';
        const resendBtnSelector = type === 'email' ? '#resend-email-otp-btn' : '#resend-phone-otp-btn';
        const resendSectionSelector = type === 'email' ? '#email-resend-section' : '#phone-resend-section';
        
        // Convert minutes to seconds
        let totalSeconds = expireInMinutes * 60;
        
        if (type === 'email') {
            clearInterval(emailResendTimer);
            emailTimerSeconds = totalSeconds;
        } else {
            clearInterval(phoneResendTimer);
            phoneTimerSeconds = totalSeconds;
        }
        
        // Hide resend section initially
        $(resendSectionSelector).hide();
        $(timerSelector).show();
        
        const timer = setInterval(function() {
            const minutes = Math.floor(totalSeconds / 60);
            const secs = totalSeconds % 60;
            $(timerSelector).text(String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0'));
            
            if (totalSeconds <= 0) {
                clearInterval(timer);
                // Hide timer and show resend section after OTP expires
                $(timerSelector).hide();
                $(resendSectionSelector).show();
                $(resendBtnSelector).prop('disabled', false);
                if (type === 'email') {
                    emailResendTimer = null;
                } else {
                    phoneResendTimer = null;
                }
            }
            totalSeconds--;
            
            if (type === 'email') {
                emailTimerSeconds = totalSeconds;
            } else {
                phoneTimerSeconds = totalSeconds;
            }
        }, 1000);
        
        if (type === 'email') {
            emailResendTimer = timer;
        } else {
            phoneResendTimer = timer;
        }
    }
    
    // ============ EMAIL CHANGE HANDLERS ============
    
    // Reset email modal when opened
    $('#changeEmailModal').on('show.bs.modal', function() {
        $('#email-step-1').show();
        $('#email-step-2').hide();
        $('#new-email-value').val('');
        $('#new-email-error').text('');
        clearOtpBoxes('#email-otp-container');
        $('#email-otp-error').text('');
        emailTempOtpId = null;
        clearInterval(emailResendTimer);
        $('#email-resend-timer').text('').show();
        $('#email-resend-section').hide();
    });
    
    // Setup OTP boxes for email
    setupOtpBoxes('#email-otp-container');
    
    // Function to handle email verification
    function handleEmailVerify() {
        const value = $('#new-email-value').val();
        const errorEl = $('#new-email-error');
        errorEl.text('');
        
        if (!value) {
            errorEl.text(window.vendorProfileLang?.fieldRequired || 'This field is required.');
            return;
        }
        
        if (!isValidEmail(value)) {
            errorEl.text(window.vendorProfileLang?.validEmail || 'Enter a valid email.');
            return;
        }
        
        $('#verify-email-btn').prop('disabled', true).text(window.vendorProfileLang?.sending || 'Sending...');
        
        $.ajax({
            url: window.vendorProfileRoutes?.sendChangeOtp || '/vendor/send-change-otp',
            method: 'POST',
            data: {
                _token: token,
                type: 'email',
                value: value
            },
            success: function(response) {
                if (response.status === 'success') {
                    emailTempOtpId = response.temp_otp_id;
                    $('#email-step-1').hide();
                    $('#email-step-2').show();
                    $('#email-otp-address').text(value);
                    // Start timer with expiration time from server (default 5 minutes)
                    const expireInMinutes = response.otp_expire_in || 5;
                    startResendTimer('email', expireInMinutes);
                    // Focus first OTP box
                    $('#email-otp-container .email-otp-box').first().focus();
                } else {
                    errorEl.text(response.message || (window.vendorProfileLang?.failedToSendOtp || 'Failed to send OTP.'));
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || (window.vendorProfileLang?.failedToSendOtp || 'Failed to send OTP.');
                errorEl.text(error);
            },
            complete: function() {
                $('#verify-email-btn').prop('disabled', false).text(window.vendorProfileLang?.verify || 'Verify');
            }
        });
    }
    
    // Handle Verify Email button click
    $('#verify-email-btn').on('click', handleEmailVerify);
    
    // Handle Enter key on email input
    $('#new-email-value').on('keypress', function(e) {
        if (e.which === 13 || e.keyCode === 13) {
            e.preventDefault();
            handleEmailVerify();
        }
    });
    
    // Handle Submit Email OTP button
    $('#submit-email-otp-btn').on('click', function() {
        const otp = getOtpFromBoxes('#email-otp-container');
        const errorEl = $('#email-otp-error');
        errorEl.text('');
        
        if (!otp || otp.length !== 6) {
            errorEl.text(window.vendorProfileLang?.validOtp || 'Please enter a valid 6-digit OTP.');
            return;
        }
        
        $(this).prop('disabled', true).text(window.vendorProfileLang?.verifying || 'Verifying...');
        
        $.ajax({
            url: window.vendorProfileRoutes?.verifyChangeOtp || '/vendor/verify-change-otp',
            method: 'POST',
            data: {
                _token: token,
                type: 'email',
                otp: otp,
                temp_otp_id: emailTempOtpId
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#email').val(response.new_value);
                    // Show attractive success message
                    const successHtml = `
                        <div class="text-center py-5">
                            <div class="success-animation mb-4">
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" style="width: 100px; height: 100px; margin: 0 auto;">
                                    <circle class="checkmark-circle" cx="60" cy="60" r="50" fill="none" stroke="#28a745" stroke-width="4" style="stroke-dasharray: 314; stroke-dashoffset: 314; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;"/>
                                    <path class="checkmark-check" fill="none" stroke="#28a745" stroke-width="4" d="M30 60 L50 80 L90 40" style="stroke-dasharray: 70; stroke-dashoffset: 70; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold text-success mb-3" style="color: #28a745 !important;">${window.vendorProfileLang?.verificationSuccessful || 'Verification Successful!'}</h4>
                            <p class="text-muted mb-4" style="font-size: 16px;">${response.message}</p>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="spinner-border spinner-border-sm text-success" role="status" style="width: 16px; height: 16px;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="text-muted small">${window.vendorProfileLang?.reloadingPage || 'Reloading page...'}</span>
                            </div>
                        </div>
                    `;
                    $('#email-step-2').html(successHtml);
                    // Reload page after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    errorEl.text(response.message || (window.vendorProfileLang?.invalidOtp || 'Invalid OTP.'));
                    clearOtpBoxes('#email-otp-container');
                    $('#email-otp-container .email-otp-box').first().focus();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || (window.vendorProfileLang?.failedToVerifyOtp || 'Failed to verify OTP.');
                errorEl.text(error);
                clearOtpBoxes('#email-otp-container');
                $('#email-otp-container .email-otp-box').first().focus();
            },
            complete: function() {
                $('#submit-email-otp-btn').prop('disabled', false).text(window.vendorProfileLang?.verify || 'Verify');
            }
        });
    });
    
    // Handle Resend Email OTP button
    $('#resend-email-otp-btn').on('click', function() {
        if (!emailTempOtpId) {
            alert(window.vendorProfileLang?.invalidRequest || 'Invalid request.');
            return;
        }
        
        $(this).prop('disabled', true);
        
        $.ajax({
            url: window.vendorProfileRoutes?.resendChangeOtp || '/vendor/resend-change-otp',
            method: 'POST',
            data: {
                _token: token,
                temp_otp_id: emailTempOtpId
            },
            success: function(response) {
                if (response.status === 'success') {
                    clearOtpBoxes('#email-otp-container');
                    $('#email-otp-error').text('');
                    // Hide resend section and show timer
                    $('#email-resend-section').hide();
                    $('#email-resend-timer').show();
                    // Restart timer with expiration time from server
                    const expireInMinutes = response.otp_expire_in || 5;
                    startResendTimer('email', expireInMinutes);
                    $('#email-otp-container .email-otp-box').first().focus();
                } else {
                    alert(response.message || (window.vendorProfileLang?.failedToResendOtp || 'Failed to resend OTP.'));
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || (window.vendorProfileLang?.failedToResendOtp || 'Failed to resend OTP.');
                alert(error);
            },
            complete: function() {
                // Timer will enable the button
            }
        });
    });
    
    // ============ PHONE CHANGE HANDLERS ============
    
    // Reset phone modal when opened
    $('#changePhoneModal').on('show.bs.modal', function() {
        $('#phone-step-1').show();
        $('#phone-step-2').hide();
        $('#new-phone-value-modal').val('');
        $('#new-phone-error').text('');
        clearOtpBoxes('#phone-otp-container');
        $('#phone-otp-error').text('');
        phoneTempOtpId = null;
        clearInterval(phoneResendTimer);
        $('#phone-resend-timer').text('').show();
        $('#phone-resend-section').hide();
    });
    
    // Setup OTP boxes for phone
    setupOtpBoxes('#phone-otp-container');
    
    // Function to handle phone verification
    function handlePhoneVerify() {
        let phoneInput = $('#new-phone-value-modal');
        let value = phoneInput.val();
        let dialCode = $('input[name="dial_code"]').val();
        
        // Get dial code and phone number separately if intlTelInput is used
        if (phoneInput.data('intlTelInput')) {                    
            const iti = phoneInput.data('intlTelInput');
            if (!iti.isValidNumber()) {
                $('#new-phone-error').text(window.vendorProfileLang?.validPhone || 'Please enter a valid phone number.');
                return;
            }
        }                
        
        const errorEl = $('#new-phone-error');
        errorEl.text('');
        
        if (!value) {
            errorEl.text(window.vendorProfileLang?.fieldRequired || 'This field is required.');
            return;
        }
        
        $('#verify-phone-btn').prop('disabled', true).text(window.vendorProfileLang?.sending || 'Sending...');
        
        $.ajax({
            url: window.vendorProfileRoutes?.sendChangeOtp || '/vendor/send-change-otp',
            method: 'POST',
            data: {
                _token: token,
                type: 'phone',
                value: value,
                dial_code: dialCode
            },
            success: function(response) {
                if (response.status === 'success') {
                    phoneTempOtpId = response.temp_otp_id;
                    $('#phone-step-1').hide();
                    $('#phone-step-2').show();
                    // Display full phone number with dial code
                    const fullPhone = dialCode ? dialCode + value : value;
                    $('#phone-otp-address').text(fullPhone);
                    // Start timer with expiration time from server (default 5 minutes)
                    const expireInMinutes = response.otp_expire_in || 5;
                    startResendTimer('phone', expireInMinutes);
                    // Focus first OTP box
                    $('#phone-otp-container .phone-otp-box').first().focus();
                } else {
                    errorEl.text(response.message || (window.vendorProfileLang?.failedToSendOtp || 'Failed to send OTP.'));
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || (window.vendorProfileLang?.failedToSendOtp || 'Failed to send OTP.');
                errorEl.text(error);
            },
            complete: function() {
                $('#verify-phone-btn').prop('disabled', false).text(window.vendorProfileLang?.verify || 'Verify');
            }
        });
    }
    
    // Handle Verify Phone button click
    $('#verify-phone-btn').on('click', handlePhoneVerify);
    
    // Handle Enter key on phone input
    $('#new-phone-value-modal').on('keypress', function(e) {
        if (e.which === 13 || e.keyCode === 13) {
            e.preventDefault();
            handlePhoneVerify();
        }
    });
    
    // Handle Submit Phone OTP button
    $('#submit-phone-otp-btn').on('click', function() {
        const otp = getOtpFromBoxes('#phone-otp-container');
        const errorEl = $('#phone-otp-error');
        errorEl.text('');
        
        if (!otp || otp.length !== 6) {
            errorEl.text(window.vendorProfileLang?.validOtp || 'Please enter a valid 6-digit OTP.');
            return;
        }
        
        $(this).prop('disabled', true).text(window.vendorProfileLang?.verifying || 'Verifying...');
        
        $.ajax({
            url: window.vendorProfileRoutes?.verifyChangeOtp || '/vendor/verify-change-otp',
            method: 'POST',
            data: {
                _token: token,
                type: 'phone',
                otp: otp,
                temp_otp_id: phoneTempOtpId
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#phone').val(response.new_value);
                    // Show attractive success message
                    const successHtml = `
                        <div class="text-center py-5">
                            <div class="success-animation mb-4">
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" style="width: 100px; height: 100px; margin: 0 auto;">
                                    <circle class="checkmark-circle" cx="60" cy="60" r="50" fill="none" stroke="#28a745" stroke-width="4" style="stroke-dasharray: 314; stroke-dashoffset: 314; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;"/>
                                    <path class="checkmark-check" fill="none" stroke="#28a745" stroke-width="4" d="M30 60 L50 80 L90 40" style="stroke-dasharray: 70; stroke-dashoffset: 70; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold text-success mb-3" style="color: #28a745 !important;">${window.vendorProfileLang?.verificationSuccessful || 'Verification Successful!'}</h4>
                            <p class="text-muted mb-4" style="font-size: 16px;">${response.message}</p>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="spinner-border spinner-border-sm text-success" role="status" style="width: 16px; height: 16px;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="text-muted small">${window.vendorProfileLang?.reloadingPage || 'Reloading page...'}</span>
                            </div>
                        </div>
                    `;
                    $('#phone-step-2').html(successHtml);
                    // Reload page after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    errorEl.text(response.message || (window.vendorProfileLang?.invalidOtp || 'Invalid OTP.'));
                    clearOtpBoxes('#phone-otp-container');
                    $('#phone-otp-container .phone-otp-box').first().focus();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || (window.vendorProfileLang?.failedToVerifyOtp || 'Failed to verify OTP.');
                errorEl.text(error);
                clearOtpBoxes('#phone-otp-container');
                $('#phone-otp-container .phone-otp-box').first().focus();
            },
            complete: function() {
                $('#submit-phone-otp-btn').prop('disabled', false).text(window.vendorProfileLang?.verify || 'Verify');
            }
        });
    });
    
    // Handle Resend Phone OTP button
    $('#resend-phone-otp-btn').on('click', function() {
        if (!phoneTempOtpId) {
            alert(window.vendorProfileLang?.invalidRequest || 'Invalid request.');
            return;
        }
        
        $(this).prop('disabled', true);
        
        $.ajax({
            url: window.vendorProfileRoutes?.resendChangeOtp || '/vendor/resend-change-otp',
            method: 'POST',
            data: {
                _token: token,
                temp_otp_id: phoneTempOtpId
            },
            success: function(response) {
                if (response.status === 'success') {
                    clearOtpBoxes('#phone-otp-container');
                    $('#phone-otp-error').text('');
                    // Hide resend section and show timer
                    $('#phone-resend-section').hide();
                    $('#phone-resend-timer').show();
                    // Restart timer with expiration time from server
                    const expireInMinutes = response.otp_expire_in || 5;
                    startResendTimer('phone', expireInMinutes);
                    $('#phone-otp-container .phone-otp-box').first().focus();
                } else {
                    alert(response.message || (window.vendorProfileLang?.failedToResendOtp || 'Failed to resend OTP.'));
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || (window.vendorProfileLang?.failedToResendOtp || 'Failed to resend OTP.');
                alert(error);
            },
            complete: function() {
                // Timer will enable the button
            }
        });
    });
    
    // Email validation helper
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // Update form submission to exclude email and phone
    $('#userEdit').on('submit', function(e) {
        $('#email, #phone').prop('disabled', false);
        var form = $(this);
        form.find('input[name="email"]').removeAttr('name');
        form.find('input[name="phone"]').removeAttr('name');
    });
    
})(jQuery);
