"use strict";
if ($('.main-body .page-wrapper').find('#brand-add-container').length ||$('.main-body .page-wrapper').find('#brand-edit-container').length) {

    $("#validatedCustomFile").on('change', function() {
        //get uploaded filename
        var files = [];
        for (var i = 0; i < $(this)[0].files.length; i++) {
            files.push($(this)[0].files[i].name);
        }
        $(this).next('.custom-file-label').html(files.join(', '));

        //image validation
        var file = this.files[0];
        var fileType = file["type"];
        var validImageTypes = ["image/jpg", "image/jpeg", "image/png"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            $('#divNote').show();
            $('#note_txt_1').hide();
            $('#note_txt_2').html('<h6> <span class="text-danger font-weight-bolder">' +jsLang('Invalid file extension') + '</span> </h6> <span class="badge badge-danger">' + jsLang('Note') + '!</span> ' + jsLang('Allowed File Extensions: jpg, png, gif, bmp'));
            $('#note_txt_2').show();
            return false;
        } else {
            $('#note_txt_2, #note_txt_1').hide();
            return true;
        }
    });


    if ($('.main-body .page-wrapper').find('.vendor-brand-add').length) {
        $(document).on('keyup', '#name', function() {
            var str = this.value.replace(/[&\/\\#@,+()$~%.'":*?<>{}]/g, "");
            $('#slug').val(str.trim().toLowerCase().replace(/\s/g, "-"));
    
            if ($('#name').val().length >= 3) {
                brandSuggestion();
            } else {
                removeSuggestion();
            }
        });
    }

   

    function brandSuggestion()
    {
        $.ajax({
            url: SITE_URL + '/brands/suggestion',
            type: "GET",
            data: {
                parnet_id : $('#parentBlock').css('display') == 'none' ? null : $('#parent_id').val(),
                name : $('#name').val(),
            },
            dataType: "json",
            success:function(data) {
                if (typeof data.id != 'undefined') {
                    $('#has_brand').removeClass('display_none');
                    let assignLink = `${data.name} ${jsLang('found!')} ${jsLang('Please')}<a href="javascript:void(0)" data-brand_id = ${data.id} class="assigned_brand" id="assigned_brand">&nbsp;${jsLang('click here')}&nbsp;</a> ${jsLang('to assign')}`
                    $('#has_brand').html(assignLink);

                    $('#assigned_brand').on("click", function () {
                        assignBrand();
                    });
                } else {
                    removeSuggestion();
                }
            }
        });
    }

    function removeSuggestion()
    {
        $('#has_brand').empty();
        $('#has_brand').addClass('display_none');
    }

    function assignBrand()
    {
        let brandId = $('#assigned_brand').data('brand_id');

        swal({
            title: jsLang("Are you sure?"),
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "POST",
                        url: SITE_URL + '/brands/assign-vendor',
                        data: {
                            "_token": token,
                            brand_id: brandId,
                        },
                        success: function (data) {

                            if (data.status == 1) {
                                swal(jsLang('Assigned Successfully'), {
                                    icon: "success",
                                    buttons: [false, jsLang('Ok')],
                                });
                                $('#has_brand').addClass('display_none');
                                $('#has_brand').empty();
                                resetForm();
                            } else {
                                swal(jsLang('Something went wrong, please try again.'), {
                                    icon: "error",
                                    buttons: [false, jsLang('Ok')],
                                });

                            }
                        }
                    });
                }
            });
    }

      /* reset from */
    function resetForm()
    {
        $('#status').val("Active").trigger('change');
        $('#name').empty();
        document.getElementById("brandAdd").reset();
    }

}
if ($('.main-body .page-wrapper').find('#brand-list-container').length) {
    // For export csv
    $(document).on("click", "#csv, #pdf", function(event) {
        event.preventDefault();
        window.location = SITE_URL + "/brands/" + this.id;
    });
}
