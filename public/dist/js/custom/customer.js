$(document).on('click', '#btnSubmit1', function () {
    let btn = $('form').find(':submit');
    let spinner = $('.spinner-border');
    setTimeout(() => {
        btn.text(jsLang('Save')).removeClass('disabled-btn');
        spinner.remove();

    }, 3000);
});

let loader = `<option value="">${jsLang('Loading')}...</option>`;
let errorMsg = jsLang(':x is not available.');

$('#country').on('change', function () {
    getState($('#country').find(':selected').attr('data-country'));
});

function getState(countryCode) {

        
    $.ajax({
        url: BASE_URL + "/geo-locale/countries/" + countryCode + "/states",
        type: "GET",
        dataType: 'json',
        beforeSend: function () {
            $('#state').attr('disabled', 'true');
            $('#state').html(loader);
        },
        success: function (result) {
            $("#state").removeAttr("disabled");
            $('#state').html('');
            
            $.each(result.data, function (key, value) {
                $("#state").append(`'<option data-state="${value.code}" value="${value.code}">${value.name}</option>'`);
            });
            
            if (result.length <= 0 && result.data.length <= 0) {
                errorMsg = errorMsg.replace(":x", 'State');
                $('#state').html(`<option value="">${errorMsg}</option>`);
            }

            $("#state").trigger('change');
        }
    });
    
}

$('#state').on('change', function () {
    getCity($('#state').find(':selected').attr('data-state'), $('#country').find(':selected').attr('data-country'));

});

function getCity(siso, ciso) {

    var ajaxUrl = BASE_URL + "/admin/countries/" + ciso + "/cities";

    if (siso !== undefined && siso !== '' && siso != null) {
        ajaxUrl = BASE_URL + "/geo-locale/countries/" + ciso + "/states/" + siso + "/cities";
    }

    $.ajax({
        url: ajaxUrl,
        type: "GET",
        dataType: 'json',
        beforeSend: function () {
            $('#city').attr('disabled', 'true');
            $('#city').html(loader);
        },
        success: function (result) {            

            $("#city").removeAttr("disabled");
            $('#city').html('');

            $.each(result.data, function (key, value) {
                $("#city").append(`'<option value="${value.name}">${value.name}</option>'`);
            });

            $("#city").trigger('change');

            if (result.length <= 0 && result.data.length <= 0) {
                alert('hi');
                errorMsg = errorMsg.replace(":x", 'City');
                $('#city').html(`<option value="">${errorMsg}</option>`);
            }
        }
    });
}