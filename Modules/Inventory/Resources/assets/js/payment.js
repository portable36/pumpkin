'use strict';
        $(document).ready(function() {
            var $amountInputs = $('.amount-input');
            amountPaid = parseFloat(amountPaid) || 0;
            
            // Auto-fill amount starting from oldest purchase orders
            if (amountPaid > 0) {
                distributePayment(amountPaid);
            }
            
            function distributePayment(totalAmount) {
                // Ensure totalAmount is a valid positive number
                var remaining = parseFloat(totalAmount) || 0;
                remaining = Math.max(0, remaining);
                
                // Convert jQuery collection to array and reverse to process oldest first
                var inputsArray = $amountInputs.toArray().reverse();
                
                inputsArray.forEach(function(input) {
                    var $input = $(input);
                    var balance = parseFloat($input.data('balance'));
                    
                    if (remaining > 0 && balance > 0) {
                        var amountToFill = Math.min(remaining, balance);
                        $input.val(amountToFill);
                        remaining -= amountToFill;
                    } else {
                        // Set to 0 if no remaining amount or no balance
                        $input.val(0);
                    }
                });
                
                // Update total after distributing payment
                updateTotalAmount();
            }
            
            // Function to calculate and display total payment amount
            function updateTotalAmount() {
                var total = 0;
                $amountInputs.each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    total += value;
                });
                
                // Format and display the total
                $('#total-payment-amount').text(total.toFixed(2));
            }

            // Function to show notification
            function showNotification(type, message) {
                var alertClass = 'alert-' + type;
                var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    '<strong>' + message + '</strong>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>';
                
                // Remove any existing notifications
                $('.payment-notification').remove();
                
                // Add new notification at the top of the form
                var $notification = $(alertHtml).addClass('payment-notification');
                $('#payment-form').prepend($notification);
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $notification.fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            // Function to show inline error under input
            function showInputError($input, message) {
                // Remove existing error for this input
                $input.closest('.input-wrapper').find('.input-error').remove();
                
                // Add error message
                var $error = $('<div class="input-error text-danger small mt-1">' + message + '</div>');
                $input.closest('.input-wrapper').append($error);
                
                // Remove error after 3 seconds
                setTimeout(function() {
                    $error.fadeOut(function() {
                        $(this).remove();
                    });
                }, 3000);
            }

            // Validate amount inputs
            $amountInputs.on('input', function() {
                var $input = $(this);
                var balance = parseFloat($input.data('balance'));
                var value = parseFloat($input.val());

                if (value > balance) {
                    $input.val(balance);
                    showInputError($input, jsLang('Amount cannot exceed the balance amount.'));
                }
                if (value < 0) {
                    $input.val(0);
                }
                
                // Update total when any input changes
                updateTotalAmount();
            });
            
            // Calculate initial total
            updateTotalAmount();

            // Form submission validation
            $('#payment-form').on('submit', function(e) {
                var hasValidPayment = false;
                var hasInvalidAmount = false;

                $amountInputs.each(function() {
                    var $input = $(this);
                    var amount = parseFloat($input.val()) || 0;

                    if (amount > 0) {
                        hasValidPayment = true;
                    }

                    var balance = parseFloat($input.data('balance'));
                    if (amount > balance) {
                        hasInvalidAmount = true;
                    }
                });

                if (!hasValidPayment) {
                    e.preventDefault();
                    showNotification('danger', jsLang('Please enter at least one payment amount.'));
                    return false;
                }

                if (hasInvalidAmount) {
                    e.preventDefault();
                    showNotification('danger', jsLang('One or more payment amounts exceed the balance amount.'));
                    return false;
                }

                return true;
            });
        });
