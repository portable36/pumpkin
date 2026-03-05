"use strict";

$(() => {
    $(".warningMessage").css("display", "none");
    $("#v-pills-general-tab").trigger("click");

    $("input").on("change", function () {
        $(this)
            .closest(".parent")
            .find(".warning-message")
            .addClass("alert-secondary");
        $(this).closest(".parent").find(".warningMessage").slideDown(300);
        $(this)
            .closest(".parent")
            .find("#warning-msg")
            .html(jsLang("Settings have changed, you should save them!"));
    });

    $(".checkActivity").on("change", function () {
        if ($(this).attr("checked")) {
            $(this).removeAttr("checked");
        } else {
            $(this).attr("checked", true);
        }
    });
});


if ($("#payment-type").val() !== "commission") {
    $("#commission").hide();
}
if ($("#payment-type").val() !== "flat") {
    $("#flat").hide();
}

$("#payment-type").on('change', function () {

    const selectedPaymentType = $(this).val();
    $("#commission").toggle(selectedPaymentType === "commission");
    $("#flat").toggle(selectedPaymentType === "flat");
});
