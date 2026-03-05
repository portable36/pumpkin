"use strict";

function passwordValidation() {
    var status = true;
    var errorMsg = '';
    var tmpMsg = [];
    if (uppercase && $('.password-validation').val().search(/[A-Z]/) < 0) {
        tmpMsg.push(jsLang('uppercase'));
        status = false;
    }
    if (lowercase && $('.password-validation').val().search(/[a-z]/) < 0) {
        tmpMsg.push(jsLang('lowercase'));
        status = false;
    }
    if (number && $('.password-validation').val().search(/[0-9]/) < 0) {
        tmpMsg.push(jsLang('numbers'));
        status = false;
    }
    if (symbol && $('.password-validation').val().search(/[#?!@$%^&*-]/) < 0) {
        tmpMsg.push(jsLang('symbols'));
        status = false;
    }

    if (tmpMsg.length > 0) {
        errorMsg = jsLang('Password must contain :x');
        errorMsg = errorMsg.replace(":x", tmpMsg.join(', '));
    }


    if (length && $('.password-validation').val().length < length) {
        if (errorMsg.length > 0) {
            errorMsg = jsLang('Password must contain :x and :y characters long.');
            errorMsg = errorMsg.replace(":x", tmpMsg.join(', '));
            errorMsg = errorMsg.replace(":y", length);

        } else {
            errorMsg = jsLang('Password must be at least :x characters.');
            errorMsg = errorMsg.replace(":x", length);
        }
        status = false;
    }

    if (status == false) {
        $('.password-validation-error').addClass('text-red').text(errorMsg);
        return false;
    }

    $('form').find(':submit').text(jsLang('Saving')).append(`<div class="spinner-border spinner-border-sm ml-2" role="status">`);
    $('form').find(':submit').addClass('disabled-btn');
    return true;
}

if ($('.main-body .page-wrapper').find('#carrier-add-container, #carrier-edit-container').length) {
    $('#btnSubmit').on('click', function() {
        setTimeout(() => {
            if ($('body').find('.error').length > 0) {
                $('.error').first().closest('.form-group').find('.form-control').focus();
            }
        }, 100);

    })

    $(document).on('keypress', function(event){

        setTimeout(() => {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13' && $('body').find('.error').length > 0){
                $('.error').first().closest('.form-group').find('.form-control').focus();
            }
        }, 100);
    });
}

$(document).on('keyup', '#password,#confirm_password', function () {
    $('.password-validation-error').text('');
})

$(document).on("click", ".remove-product-image", function () {
    $(this).closest(".img-box-two").remove();
});
