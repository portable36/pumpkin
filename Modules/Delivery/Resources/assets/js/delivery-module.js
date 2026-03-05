"user strict";

const triggerNotification = (msg) => {
    $(".notification-msg-bar").find(".notification-msg").html(msg);
    $(".notification-msg-bar").removeClass("smoothly-hide");
    setTimeout(() => {
        $(".notification-msg-bar").addClass("smoothly-hide"),
            $(".notification-msg-bar").find(".notification-msg").html("");
    }, 1500);
};

function updateDeliveryManStatusBadge() {
    
    $.ajax({
        url: SITE_URL + '/status',
        method: 'GET',
        success: function (response) {
            $('.delivery-man-status-badge').text(response.is_active ? jsLang('Online') : jsLang('Offline'))
                .toggleClass('theme-bg-active', response.is_active == true)
                .toggleClass('theme-bg-inactive', response.is_active == false).removeClass('spinner-border');
            triggerNotification(jsLang('Successfully update your status.'))
        },
        error: function (jqXHR) {
            triggerNotification(jqXHR?.responseJSON?.message ?? jsLang('Something went wrong, please try again.'))
        }
    });
}




$('.delivery-man-status-badge').on('click', function () {
    $(this).append(`<div class="spinner-border spinner-border-sm ml-2" role="status">`)
    updateDeliveryManStatusBadge();
})
