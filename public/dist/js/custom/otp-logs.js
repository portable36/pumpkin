"use strict";

// Initialize date range picker for OTP logs pages
if ($('.main-body .page-wrapper').find('#otp-logs-container, #otp-logs-detail-container').length) {
    $('#daterange-btn').daterangepicker(daterangeConfig(startDate, endDate), cbRange);
    cbRange(startDate, endDate);

    $(document).on("click", ".applyBtn, .ranges ul li:nth-child(1), .ranges ul li:nth-child(2), .ranges ul li:nth-child(3), .ranges ul li:nth-child(4), .ranges ul li:nth-child(5), .ranges ul li:nth-child(6), .ranges ul li:nth-child(7)", function (event) {
        event.preventDefault();
        let startFrom = $("#startfrom").val();
        let endto = $("#endto").val();
        var newOptions = {
            startFrom: startFrom,
        };
        var newOptionsTwo = {
            endto: endto,
        };
        let startDate = $("#start_date");
        let end_date = $("#end_date");
        startDate.empty();
        end_date.empty();
        $.each(newOptions, function(key,value) {
            startDate.append($("<option></option>")
                .attr("value", value).text(key));
        });
        $.each(newOptionsTwo, function(key,value) {
            end_date.append($("<option></option>")
                .attr("value", value).text(key));
        });
        $("#start_date option:first").attr('selected','selected');
        $("#end_date option:first").attr('selected','selected');
        $("#start_date").trigger("change");
    });
}

