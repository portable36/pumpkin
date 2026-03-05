"use strict";

$('#password_confirm, #password_seller').on('keyup', function(){
    let password = $("#password_seller").val();
    let confirmPassword = $("#password_confirm").val();
    if (password.length && confirmPassword.length) {
        if (password != confirmPassword) {
            $(".password-validation-match-error").addClass('text-red-500');
            $(".password-validation-match-error").text(jsLang('Passwords does not match!'));
            $(".password-validation-error").text('');
        }
        else {
            $(".password-validation-match-error").text('');
        }
    }
    $(".password-validation-error").text('');
});

function formValidation() {
    var status = true;
    var isPasswordValid = true;
    var errorMsg = "";
    var tmpMsg = [];

    if (uppercase && $(".password-validation").val().search(/[A-Z]/) < 0) {
        tmpMsg.push(jsLang("uppercase"));
        status = isPasswordValid = false;
    }
    if (lowercase && $(".password-validation").val().search(/[a-z]/) < 0) {
        tmpMsg.push(jsLang("lowercase"));
        status = isPasswordValid = false;
    }
    if (number && $(".password-validation").val().search(/[0-9]/) < 0) {
        tmpMsg.push(jsLang("numbers"));
        status = isPasswordValid = false;
    }
    if (
        symbol &&
        $(".password-validation")
            .val()
            .search(/[#?!@$%^&*-]/) < 0
    ) {
        tmpMsg.push(jsLang("symbols"));
        status = isPasswordValid = false;
    }

    if (tmpMsg.length > 0) {
        errorMsg = jsLang("Password must contain :x");
        errorMsg = errorMsg.replace(":x", tmpMsg.join(", "));
    }

    if (length && $(".password-validation").val().length < length) {
        if (errorMsg.length > 0) {
            errorMsg = jsLang(
                "Password must contain :x and :y characters long."
            );
            errorMsg = errorMsg.replace(":x", tmpMsg.join(", "));
            errorMsg = errorMsg.replace(":y", length);
        } else {
            errorMsg = jsLang("Password must be at least :x characters.");
            errorMsg = errorMsg.replace(":x", length);
        }
        status = isPasswordValid = false;
    }

    if (status == false) {
        if (!isPasswordValid) {
            $(".password-validation-error")
                .addClass("text-red-500")
                .text(errorMsg);
        }
        return false;
    }

    if (status == true) {
        return true;
    }
}

$(document).on("click", ".toggle-password", function () {
    let input = $("#password_seller");
    if (input.attr("type") === "password") {
        input.attr("type", "text");
        $(".pass-hide").removeClass("hidden");
        $(".pass-show").addClass("hidden");
    } else {
        input.attr("type", "password");
        $(".pass-show").removeClass("hidden");
        $(".pass-hide").addClass("hidden");
    }
});

$(document).on('click', "#btnSubmits", function() {
   setTimeout(goToErrorMessage, 0);
})

function goToErrorMessage() {
    $([document.documentElement, document.body]).animate({
        scrollTop: $(".error").offset().top - 170
    }, 500);
}
