'use strict';

let paymentDate = $('#payment_date').val();

$('#payment_date').daterangepicker(selectFromTo(paymentDate.length > 0 ? paymentDate : null));
