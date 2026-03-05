"use strict";

const triggerNotification = (msg) => {
    $(".notification-msg-bar").find(".notification-msg").html(msg);
    $(".notification-msg-bar").removeClass("smoothly-hide");
    setTimeout(() => {
        $(".notification-msg-bar").addClass("smoothly-hide"),
            $(".notification-msg-bar").find(".notification-msg").html("");
    }, 3000);
};

$('.dd-button.email').on('click', function() {
    $('.dd-menu.email').toggleClass('d-none');
});

var paymentDate = $('#payment_date').val();
$('#payment_date').daterangepicker(selectFromTo(paymentDate.length > 0 ? paymentDate : null));

function sendMail(type, button) {
    return new Promise((resolve, reject) => {
        let actionVal = type === 'customer' ? 1 : 3;
        let data = {
            order_id: orderId,
            action_val: actionVal,
            type: 'orderAction'
        };

        $.ajax({
            type: "POST",
            url: orderUrl,
            data: {
                "_token": token,
                data: data,
            },
            success: function (response) {
                if (response.status === 1) {
                    triggerNotification(response.message);
                    resolve(response); // Resolve on success
                } else if (typeof response.error !== 'undefined') {
                    triggerNotification(response.error);
                    reject(response.error); // Reject on error
                } else {
                    let msg = jsLang('Something went wrong, please try again.');
                    triggerNotification(msg);
                    reject(msg);
                }
            },
            error: function (xhr) {
                triggerNotification(xhr.responseJSON.message);
                reject(xhr);
            }
        });
    });
}

function handleMailButtonClick(button, type, originalText) {
    if (button.hasClass('disabled')) return;

    button.addClass('disabled').text(jsLang('Mail is sending') + '...');

    sendMail(type).finally(() => {
        button.removeClass('disabled').text(originalText);
    });
}

// Attach for both buttons
$('#send_mail_to_customer').on('click', function () {
    handleMailButtonClick($(this), 'customer', jsLang('Send Mail to Customer'));
});

$('#send_mail_to_vendor').on('click', function () {
    handleMailButtonClick($(this), 'vendor', jsLang('Send Mail to Vendor'));
});
