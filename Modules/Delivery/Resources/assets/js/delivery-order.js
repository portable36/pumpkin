"user strict";

$(() => {
    const assignCarrierModal = document.getElementById('assign-carrier-modal');
    const modalRefreshBtn = document.getElementById('assign-carrier-modal-refresh-btn');

    assignCarrierModal.addEventListener('show.bs.modal', () => {
        getAssignOrderView();
    });
    modalRefreshBtn.addEventListener('click', () => {
        getAssignOrderView();
    });
});

function getAssignOrderView() {
    let loadingSkeleton = `<div class="d-flex justify-content-center"> <div class="placeholder wave p-0 mx-2 my-2 order-sec-head"> <div class="square"></div></div></div><div class="d-flex justify-content-center"> <div class="placeholder wave p-0 mx-2 my-2 h-15p"> <div class="square"></div></div></div><div class="d-flex justify-content-center"> <div class="placeholder wave p-0 mx-2 my-2 h-15p"> <div class="square"></div></div></div><div class="d-flex justify-content-center"> <div class="placeholder wave p-0 mx-2 my-4 h-40"> <div class="square"></div></div></div><div class="d-flex justify-content-center"> <div class="placeholder wave p-0 mx-2 my-2 h-20"> <div class="square"></div></div></div><div class="d-flex justify-content-center"> <div class="placeholder wave p-0 mx-2 my-2 h-20"> <div class="square"></div></div></div>`;

    $.ajax({
        type: "GET",
        url: MAIN_URL + "/delivery/carrier/assign/view",
        data: {
            order_id: orderId
        },
        dataType: "html",
        cache: false,
        beforeSend: () => {
            $("#assign-carrier-modal-body").html(loadingSkeleton);
        },
        success: (response) => {
            if (response) {
                $("#assign-carrier-modal-body").html(response);

                const searchCarrierInput = document.getElementById('search_carrier');
                searchCarrierInput.addEventListener('input', function (event) {
                    searchAndPopulateCarrierTable(event.target.value);
                });

                $('.assign-carrier-btn').on('click', (event) => {
                    assignToOrder($(event.target));
                });
            }
        },
        fail: (jqXHR, textStatus, errorThrown) => {
            $("#assign-carrier-modal-body").html(`<div class="d-flex justify-content-center"><span>Something went wrong!</span></div>`);
        }
    });
}


function searchAndPopulateCarrierTable(searchString) {

    $.ajax({
        type: "GET",
        url: ROOT_URL + '/delivery/ajax/carrier/search/available',
        delay: 250,
        beforeSend: () => {
            $("#carrier_search_spinner").removeClass("d-none");
        },
        dataType: "json",
        data: {
            q: searchString
        },
        success: (response) => {
            let data = response.data;
            $('#carrier_table tbody').empty();

            if (data.length > 0) {
                data.forEach((element) => {
                    $("#carrier_table > tbody").append(`<tr><td class="text-center">${element.unique_id}</td><td class="text-center">${element.name}</td><td class="text-center">${element.assigned_order_count}</td><td class="text-center"><button type="button" class="btn btn-primary btn-sm assign-carrier-btn" data-id="${element.id}"><span class="fa fa-plus"></span><span class="fa fa-solid fa-cog fa-spin d-none"></span><span class="ps-1">Assign</span></button></td></tr>`);

                    $('.assign-carrier-btn').on('click', (event) => {
                        assignToOrder($(event.target));
                    });
                });
            } else {
                $("#carrier_table > tbody").append(`<tr><td colspan="4" class="text-center">No record found!</td></tr>`);
            }

            $("#carrier_search_spinner").addClass("d-none");
        }
    });
}


function assignToOrder(target) {
    let targetBtn = target;
    if (!target.is('button')) {
        targetBtn = target.parent();
    }

    let carrierId = targetBtn.data('id');

    $.ajax({
        type: "POST",
        url: ROOT_URL + '/delivery/ajax/carrier/assign',
        data: {
            _token: token,
            delivery_man_id: carrierId,
            order_id: orderId
        },
        dataType: "json",
        beforeSend: function () {
            targetBtn.find('span.fa-cog').removeClass('d-none');
            targetBtn.find('span.fa-plus').addClass('d-none');
        },
        success: (response) => {
            targetBtn.find('span.fa-plus').removeClass('d-none');
            targetBtn.find('span.fa-cog').addClass('d-none');
            $("#carrier-modal-alert").empty();

            if (response.is_assigned) {
                $("#carrier-modal-alert").append(`<div class="alert alert-success alert-dismissible fade show" role="alert">${response.message}</div>`).hide().slideDown(500);
            } else {
                $("#carrier-modal-alert").append(`<div class="alert alert-danger alert-dismissible fade show" role="alert">${response.message}</div>`).hide().slideDown(500);
            }

            $(".alert-dismissible").fadeTo(2000, 500).slideUp(2000, function () {
                $(".alert-dismissible").alert('close');
            });
        },
        fail: (jqXHR, textStatus, errorThrown) => {
            $("#carrier-modal-alert").empty();
            $("#carrier-modal-alert").append(`<div class="alert alert-danger alert-dismissible fade show" role="alert">Something went wrong!</div>`).hide().slideDown(500);

            $(".alert-dismissible").fadeTo(2000, 500).slideUp(2000, function () {
                $(".alert-dismissible").alert('close');
            });
        }
    });
}
