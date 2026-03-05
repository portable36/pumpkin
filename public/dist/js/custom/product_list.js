"use strict";
if ($(".main-body .page-wrapper").find("#item-list-container").length || $('.main-body .page-wrapper').find('#vendor-item-list-container').length) {

    $(document).on("change", ".product_type", function (event) {
        if ($(this).val() == 'Simple Product' || $(this).val() == 'Variable Product') {
            $('.sub_type').removeClass('display_none');
        } else {
            $('select[name="sub_type"]').val("").change();
            $('.sub_type').addClass('display_none');
        }
    });

    // Initialize brand select2
    if (typeof brandUrl !== 'undefined') {
        initializeSelect2Ajax('.brand-ajax', brandUrl, jsLang("Search brand"));
    }

    // Initialize category select2
    if (typeof categoryUrl !== 'undefined') {
        initializeSelect2Ajax('.category-ajax', categoryUrl, jsLang("Search category"));
    }

    // Initialize vendor select2
    if (typeof vendorUrl !== 'undefined') {
        initializeSelect2Ajax('.vendor-ajax', vendorUrl, jsLang("Search vendor"));
    }
}
