"use strict";
$(function() {
    setupDialCode('#edit_user_profile_form', 'phone');
    setupDialCode('#userEdit', 'phone');
    setupDialCode('#userAdd', 'phone');
    setupDialCode('#vandorAdd', 'phone');
    setupDialCode('#vandorEdit', 'phone');
    setupDialCode('#addressForm', 'phone');
    setupDialCode('#editAddressForm', 'phone');
    setupDialCode('#vendorUpdateForm', 'phone');
    setupDialCode('#admin-login-form', 'phone');
    setupDialCode('#vendor-registration-form', 'phone');
    setupDialCode('#admin-reset-password-form', 'phone');
    setupDialCode('#changePhoneModal', 'phone');
})

let itiInstance = null;

function initIntlTelInput(identifier) {
    itiInstance = window.intlTelInput($(identifier).get(0), {
        utilsScript: utilJs,
        showSelectedDialCode: true,
        initialCountry: defaultCountryCode ?? '',
    });

    $('.iti--allow-dropdown').addClass('w-full w-100')
}

function destroyIntlTelInput() {
    if (itiInstance) {
        itiInstance.destroy();
        itiInstance = null;
    }
}

function setDialCode(parent, identifier) {
    if ($(identifier).length == 0) {
        return;
    }
    
    if ($(parent).find('.iti__flag-container').length == 0) {
        initIntlTelInput(identifier);
        
        fixBdCode();
    }
    
    var dialCode = $(parent).find('.iti__selected-dial-code').text();

    if (!dialCode) {
        destroyIntlTelInput();
    
        initIntlTelInput(identifier);

        $(identifier).focus();
    }
    
    if ($(parent).find('input[name="dial_code"]').length) {
        $(parent).find('input[name="dial_code"]').val(dialCode)
        return;
    }
    
    $(parent).append('<input type="hidden" name="dial_code" value="' + dialCode + '" />');
}

$(document).on('keyup', 'input[name="phone"]', function() {
    $(this).val($(this).val().replace(/\D/g, ''))
})

function fixBdCode() {
    var countryData = window.intlTelInputGlobals.getCountryData();
    for (var i = 0; i < countryData.length; i++) {
        var country = countryData[i];
        if (country.iso2 == 'bd') {
            country.dialCode = '88';
        }
    }
    
    $('#iti-0__item-bd').attr('data-dial-code', '88');
    $('#iti-0__item-bd .iti__dial-code').text('+88');
    
    if ($('.iti__selected-dial-code').text() == '+880') {
        $('.iti__selected-dial-code').text('+88')
    }
    
    setTimeout(() => {
        $('input[name="phone"]').each(function () {
            var currentValue = $(this).val();
            var sanitizedValue = currentValue.replace(/\D/g, '');
            if (currentValue != '' && currentValue[0] != '0' && $('.iti__selected-dial-code').text() == '+88') {
                sanitizedValue = '0' + sanitizedValue;
            }
            
            $(this).val(sanitizedValue);
        });
    }, 1000);
}

function setupDialCode(containerId, inputName) {
    if ($(containerId).length) {   
        setDialCode(containerId, `${containerId} input[name="${inputName}"]`);

        $(`${containerId} input[name="${inputName}"]`).on("countrychange", function (e, countryData) {
            setDialCode(containerId, `${containerId} input[name="${inputName}"]`);
        });
    }
}

$(document).on("countrychange", '.cc-phone', function (e, countryData) {
    $("#my-modal").css("display", "flex");
});

$('button[type="submit"]').on('click', function () {
    setTimeout(() => {
        $('input[type="phone"]').each(function () {
            const $input = $(this);
            const $error = $input.parent().find('.error');
            
            if ($error.length > 0) {
                $input.parent().css({ "margin-bottom": "16px" });

                // Ensure this only binds once per input
                $input.on('keyup', function () {
                    $(this).parent().css({ "margin-bottom": "0px" });
                });
            }
        });
    }, 10);
});

