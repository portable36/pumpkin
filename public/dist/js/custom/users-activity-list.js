"use strict";

$(document).ready(function () {
    $(".select2").select2();

    $(".ajax_users").select2({
        ajax: {
            url: searchURI,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                };
            },
            processResults: function (jsonResponse, params) {
                // Return only array of objects - format expected by Select2.
                return {
                    results: jsonResponse.data,
                };
            },
            cache: true,
        },
        placeholder: "Search user",
        allowClear: true,
        minimumInputLength: 3,
    });

    $('.ajax_users').on('select2:open', function () {
        const input = $('.select2-search__field');
        if (input.val().length === 0) {
            input.val('   ').trigger('input'); // Trigger empty fetch
            input.val('');
        }
    });
});
