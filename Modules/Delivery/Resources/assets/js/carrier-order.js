"user strict";

const unblockEverything = () => {
    $(".blockUI").each(function () {
        $(this).parent().unblock();
    });
};

const blockElement = (element, _data = {}) => {
    let options = Object.assign(
        {},
        {
            message: `<div class="spinner-border text-warning" role="status"><span class="sr-only">Loading...</span></div>`,
            css: {
                backgroundColor: "transparent",
                border: "none",
            },
        },
        _data
    );
    element.block(options);
};

const finished = () => {
    unblockEverything();
    $('#update-order').text(jsLang('Update'));
    orderUpdate = 0;
}


var oldOrderStatusId = $('#status').val();

var orderUpdate = 0;
$('#update-order').on('click', function () {
    $(this).text(jsLang('Updating')).append(`<div class="spinner-border spinner-border-sm ml-2" role="status">`)
    blockElement($('.order-details-body'))
    blockElement($('.order-info-container'))
    updateMainStatus();
})


function updateMainStatus() {
    let orderStatusId = $('#status').val();
    $.ajax({
        url: SITE_URL + '/order/change-status',
        type: 'POST',
        data: {
            '_token': token,
            'data': {
                'status_id': $("#status").val(),
                'order_id': orderId,
            }
        },
        success: function (data) {
            if (data.status == 1) {
                triggerNotification(data.message)
                oldOrderStatusId = orderStatusId;
                $(".status").each(function () {
                    if (!$(this).hasClass('delivery') || $('#status').val() == finalOrderStatus) {
                        $(this).val(orderStatusId);
                    }
                });
            } else {
                triggerNotification(data.error)
            }

        },
        error: function () {
            triggerNotification(jsLang('Something went wrong, please try again.'))
        },
        complete: function () {
            finished();
            location.reload();
        }
    })
}