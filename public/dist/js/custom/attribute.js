"use strict";

if (
    $(".main-body .page-wrapper").find("#attribute_group-add-container")
        .length ||
    $(".main-body .page-wrapper").find("#attribute_group-edit-container")
        .length ||
    $(".main-body .page-wrapper").find("#attribute-add-container").length ||
    $(".main-body .page-wrapper").find("#attribute-edit-container").length
) {
    var rowid = 2;
    if (
        $(".main-body .page-wrapper").find("#attribute-edit-container").length
    ) {
        rowid = $("#row_id").val();
    }

    if (
        $(".main-body .page-wrapper").find("#attribute-add-container").length ||
        $(".main-body .page-wrapper").find("#attribute-edit-container").length
    ) {
        $("#values").sortable({
            distance: 5,
            delay: 300,
            opacity: 0.6,
            cursor: "move",
            handle: ".handle",
            
        });
    }
    $(document).on("click", "#btnSubmit", function (event) {
        let arr = ["v-pills-home-tab", "v-pills-profile-tab"];
        setTimeout(() => {
            for (const key in arr) {
                let target = $(`.tab-pane[aria-labelledby="${arr[key]}"]`);
                if ($(target).find(".error").length > 0) {
                    $(`#${arr[key]}`).tab('show');
                    break;
                }
            }
        }, 100);
    });


    $(document).on("click", "#add-new-value", function (event) {
        event.preventDefault();
        addAttributeValue();
    });

    function addAttributeValue() {

        var allFilled = true;
                
        $('.attribute-value').each(function() {
            if ($(this).val().trim() === '') {
                allFilled = false;
                checkValidity(this, { inErr: true });
                return false; 
            }
        });

        if (allFilled) {
            let attributValue = `<tr draggable="false" id="rowid-${rowid}">
                                    <td class="text-center handle">
                                        <i class="fa fa-bars"></i>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="values[]" class="form-control inputFieldDesign attribute-value" required 
                                            oninvalid="this.setCustomValidity('${jsLang('This field is required.')}')" id="valueChk-${rowid}">
                                            <span id="value-text-${rowid}" class="validationMsg"></span>
                                            <input type="hidden" name="row_identify[]" value="${rowid}">
                                        </div>
                                    </td>
                                    <td class="colorCode">
                                        <div class="form-group">
                                            <input type="text" class="form-control demo inputFieldDesign" name="additional_values[]">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-danger delete-row" data-row-id="${rowid}" data-bs-toggle="tooltip" data-title="${jsLang('Delete Value')}">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>`;
            rowid++;

            $("#values").append(attributValue); 
            if($('#attrType').find(":selected").val() == 'dropdown'){
                $(".colorCode").addClass("d-none");
            }
            initializeMinicolors();
        }
    }

    function resetAttributeValues() {
        rowid = 1; 
        addAttributeValue(); 
    }   

    $(document).ready(function(){
        var selectedValue = $("#attrType").val();
        if(selectedValue == 'dropdown'){
            $(".colorCode").addClass("d-none");
        }else{
            $(".colorCode").each(function() {
                if ($(this).hasClass("d-none")) {
                    $(this).removeClass("d-none");
                }
            });
        }
    });
    
    $(document).on("change", "#attrType", function(){
        resetAttributeValues();
        if($(this).val() == 'dropdown'){
            $(".colorCode").addClass("d-none");
        }else{
            $(".colorCode").each(function() {
                if ($(this).hasClass("d-none")) {
                    $(this).removeClass("d-none");
                }
            });
        }
    });
    
    function initializeMinicolors(){
        $('.demo').each(function() {
            $(this).minicolors({
                control: $(this).attr('data-control') || 'hue',
                defaultValue: $(this).attr('data-defaultValue') || '',
                format: $(this).attr('data-format') || 'hex',
                keywords: $(this).attr('data-keywords') || '',
                inline: $(this).attr('data-inline') === 'true',
                letterCase: $(this).attr('data-letterCase') || 'lowercase',
                opacity: $(this).attr('data-opacity'),
                position: $(this).attr('data-position') || 'bottom',
                swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
                change: function(value, opacity) {
                    if (!value) return;
                    if (opacity) value += ', ' + opacity;
                    if (typeof console === 'object') {
                    }
                },
                theme: 'bootstrap'
            });
        });
    }

    $(document).on("click", ".delete-row", function (e) {
        e.preventDefault();
        $(this).tooltip('dispose');
        var idtodelete = $(this).attr("data-row-id");
        $("#rowid-" + idtodelete).remove();
    });

    $(document).on('keyup', '#name', function() {
        var str = this.value.replace(/[&\/\\#@,+()$~%.'":*?<>{}]/g, "");
        $('#slug').val(str.trim().toLowerCase().replace(/\s/g, "-"));

        if ($('#name').val().length >= 2) {
            attrbuteSuggestion();
        } else {
            removeSuggestion();
        }
    });

    
    function attrbuteSuggestion()
    {
        $.ajax({
            url: SITE_URL + '/attributes/suggestion',
            type: "GET",
            data: {
                parnet_id : $('#parentBlock').css('display') == 'none' ? null : $('#parent_id').val(),
                name : $('#name').val(),
            },
            dataType: "json",
            success:function(data) {
                if (typeof data.id != 'undefined') {
                    $('#has_attribute').removeClass('display_none');
                    let assignLink = `${data.name} ${jsLang('found!')} ${jsLang('Please')}<a href="javascript:void(0)" data-attribute_id = ${data.id} class="assigned_attribute" id="assigned_attribute">&nbsp;${jsLang('click here')}&nbsp;</a> ${jsLang('to assign')}`
                    $('#has_attribute').html(assignLink);

                    $('#has_attribute').on("click", function () {
                        assignAttribute();
                    });
                } else {
                    removeSuggestion();
                }
            }
        });
    }

    function removeSuggestion()
    {
        $('#has_attribute').empty();
        $('#has_attribute').addClass('display_none');
    }

    function assignAttribute()
    {
        let attribute_id = $('#assigned_attribute').data('attribute_id');

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
                        url: SITE_URL + '/attributes/assign-vendor',
                        data: {
                            "_token": token,
                            attribute_id: attribute_id,
                        },
                        success: function (data) {

                            if (data.status == 1) {
                                swal(jsLang('Assigned Successfully'), {
                                    icon: "success",
                                    buttons: [false, jsLang('Ok')],
                                }).then(function () {
                                    window.location.href = SITE_URL + '/all-attributes';
                                });
                                $('#has_attribute').addClass('display_none');
                                $('#has_attribute').empty();
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
if ($(".main-body .page-wrapper").find("#attribute-list-container").length) {
    // For export csv
    $(document).on("click", "#csv, #pdf", function (event) {
        event.preventDefault();
        window.location = SITE_URL + "/attributes/" + this.id;
    });
}

if (
    $(".main-body .page-wrapper").find("#attribute_group-list-container").length
) {
    // For export csv
    $(document).on("click", "#csv, #pdf", function (event) {
        event.preventDefault();
        window.location = SITE_URL + "/attribute-groups/" + this.id;
    });
}

$(document).on('drag', '.edit-attribute', function() {
    $(this).tooltip('dispose');
})

$(document).on('mouseenter', '.edit-attribute', function() {
    $(this).tooltip('enable');
})
