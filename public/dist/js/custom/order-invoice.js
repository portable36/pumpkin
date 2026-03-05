'use strict';

var vendorId = $('#vendor_id').val() ?? 0;
var orderDate = $('#order_date').val();
$('#order_date').daterangepicker(selectFromTo(orderDate.length > 0 ? orderDate : null));

initializeSelect2Ajax('#vendor_id', SITE_URL + '/inventory/find-vendor-with-ajax', jsLang("Search for vendor by name"));

const triggerNotification = (msg) => {
    $(".notification-msg-bar").find(".notification-msg").html(msg);
    $(".notification-msg-bar").removeClass("smoothly-hide");
    setTimeout(() => {
        $(".notification-msg-bar").addClass("smoothly-hide"),
            $(".notification-msg-bar").find(".notification-msg").html("");
    }, 3000);
};

$('#vendor_id').on('change', function() {
    $('#location_id').val(null).trigger('change');
    $('.select-user').val(null).trigger('change');
    clearProductArea();
    fetchVendorDefaultLocation($(this).val());
});

function fetchVendorDefaultLocation(vendorId)
{
    if (vendorId) {
        $.ajax({
            url: SITE_URL + '/inventory/find-vendor-default-location-with-ajax',
            dataType: "json",
            type: "POST",
            data: {
                _token: token,
                vendorId: vendorId
            },
            success: function (data) {
                if (data.status == 200) {
                    $('#location_id').append(new Option(data.location.name, data.location.id, true, true))
                        .trigger('change');
                }
            }
        })
    };
}

$('#location_id').on('change', function() {
    $('.product-dropdown').html('');
    clearProductArea();
});

$("#location_id").select2({
    ajax: {
        url: SITE_URL + '/inventory/find-location-with-ajax',
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page,
                vendorId: $('#vendor_id').val(),
            };
        },
        processResults: function (data, params) {
            let results = data.data;
            return {
                results: results
            };
        },
        cache: true,
    },
    placeholder: jsLang("Search for location by name."),
    language: {
        noResults: function() {
            return jsLang('Did you forget to select the vendor?');
        }
    },

    escapeMarkup: function (markup) {
        return markup;
    },
    minimumInputLength: 3,
});

triggerSelect2('#location_id');

$(".select-user").select2({
    ajax: {
        url: GLOBAL_URL + '/vendor/find-customer-by-vendor-ajax',
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page,
                vendorId: $('#vendor_id').val(),
            };
        },
        processResults: function (data, params) {
            let results = data.data;
            return {
                results: results
            };
        },
        cache: true,
    },
    placeholder: jsLang("Search for customer by name."),
    language: {
        noResults: function() {
            return jsLang('Did you forget to select the vendor?');
        }
    },

    escapeMarkup: function (markup) {
        return markup;
    },
    minimumInputLength: 3,
});

triggerSelect2('.select-user');

// ------------------------ Handle Address -----------------------------
$('.select-user').on('change', function () {
    $.ajax({
        url: SITE_URL + '/user-address',
        type: 'POST',
        data: {
            '_token': token,
            'user_id': $(this).val(),
            'vendor_id': $('select[name="vendor_id"]').val()
        },
        dataType: "json",
        success: function (data) {            
            // null check for primary address and all addresses
            if (data.primary_address) {            
                $('.billing-address-name, .shipping-address-name').text(data.primary_address.name);
                $('.billing-address-phone, .shipping-address-phone').text(data.primary_address.phone);
                $('.billing-address-email, .shipping-address-email').text(data.primary_address.email);
                $('.billing-address-street, .shipping-address-street').text(data.primary_address.address_2 ? `${data.primary_address.address_1}, ${data.primary_address.address_2}` : data.primary_address.address_1);
                $('.billing-address-city, .shipping-address-city').text(data.primary_address.city);
                $('.billing-address-state, .shipping-address-state').text(data.primary_address.state_name);
                $('.billing-address-zip, .shipping-address-zip').text(data.primary_address.zip);
                $('.billing-address-country, .shipping-address-country').text(data.primary_address.country_name);
                $('.billing-address-type-of-place, .shipping-address-type-of-place').text('(' + data.primary_address.type_of_place + ')');

                $('.billing-first-name, .shipping-first-name').val(data.primary_address.name);
                $('.billing-last-name, .shipping-last-name').val('');
                $('.billing-company-name, .shipping-company-name').val(data.primary_address.company_name);
                $('.billing-email, .shipping-email').val(data.primary_address.email);
                $('.billing-phone, .shipping-phone').val(data.primary_address.phone);
                $('.billing-address-1, .shipping-address-1').val(data.primary_address.address_1);
                $('.billing-address-2, .shipping-address-2').val(data.primary_address.address_2);
                $('.billing-zip, .shipping-zip').val(data.primary_address.zip);
                $('.billing-country, .shipping-country').val(data.primary_address.country);
                $('.billing-state, .shipping-state').val(data.primary_address.state);
                $('.billing-city, .shipping-city').val(data.primary_address.city);
                $('.billing-type-of-place, .shipping-type-of-place').val(data.primary_address.type_of_place);

                debouncedCalculateTaxShipping();
            } else {
                $('.billing-address-name, .shipping-address-name').text('---------------');
                $('.billing-address-phone, .shipping-address-phone').text('---------------');
                $('.billing-address-email, .shipping-address-email').text('--------------------');
                $('.billing-address-street, .shipping-address-street').text('-----------------------------');
                $('.billing-address-city, .shipping-address-city').text('--------------');
                $('.billing-address-state, .shipping-address-state').text('-------------');
                $('.billing-address-zip, .shipping-address-zip').text('---------------');
                $('.billing-address-country, .shipping-address-country').text('-----------');
                $('.billing-address-type-of-place, .shipping-address-type-of-place').text('');
            }            

            if (data.all_addresses.length !== 0) {
                $('#billing_address_list, #shipping_address_list').html(''); // Clear existing addresses
                $('.billing-address-edit, .shipping-address-edit').removeClass('d-none');
                data.all_addresses.forEach(address => {
                    // Determine default status and corresponding Bootstrap classes
                    const isDefault = address.is_default;
                    const cardBorderColor = isDefault ? 'border-primary' : 'border-light-subtle';
                    const cardBgColor = isDefault ? 'bg-primary-subtle' : 'bg-white';
                    
                    // Default Indicator Badge
                    const defaultBadge = isDefault
                        ? `<span class="badge rounded-pill text-bg-primary position-absolute top-0 end-0 mt-3 me-3 px-3 py-2 fw-semibold shadow-sm">${jsLang('Default')}</span>`
                        : '';
                    
                    $('#billing_address_list, #shipping_address_list').append(`
                        <div class="col-md-6 mb-4">
                            <div class="card address-card h-100 selectable-address border ${cardBorderColor} ${cardBgColor} shadow-sm" 
                                data-address-id="${address.id}" 
                                data-address-city="${address.city || ''}" 
                                data-address-state="${address.state || ''}"
                                data-address-state-name="${address.state_name || ''}"

                                data-address-country="${address.country || ''}"
                                data-address-country-name="${address.country_name || ''}"
                                data-address-zip="${address.zip || ''}"
                                data-address-type-of-place="${address.type_of_place || ''}"
                                data-address-address_1="${address.address_1 || ''}"
                                data-address-address_2="${address.address_2 || ''}"
                                data-address-company="${address.company_name || ''}"

                                style="border-radius: 0.75rem; transition: all 0.2s ease-in-out; cursor: pointer;"
                                onmouseover="this.classList.add('shadow-lg')"
                                onmouseout="this.classList.remove('shadow-lg')">
                                
                                <div class="card-body position-relative p-4 d-flex flex-column">

                                    ${defaultBadge}

                                    <div class="mb-4">
                                        <p class="fs-6 fw-bold text-dark mb-1 me-5">
                                            ${address.address_1 || jsLang('Address Not Set')}
                                        </p>
                                        ${address.address_2 ? `<p class="text-muted mb-0 me-5 small">${address.address_2}</p>` : ''}
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <div class="row g-2 text-dark">
                                            
                                            <div class="col-6">
                                                <small class="text-secondary d-block">${jsLang('City / State')}</small>
                                                <span class="d-block fw-medium">${address.city || '-'}, ${address.state_name || '-'}</span>
                                            </div>
                                            
                                            <div class="col-6">
                                                <small class="text-secondary d-block">${jsLang('Zip Code')}</small>
                                                <span class="d-block fw-medium">${address.zip || '-'}</span>
                                            </div>

                                            <div class="col-12 mt-3">
                                                <small class="text-secondary d-block">${jsLang('Country')}</small>
                                                <span class="d-block fw-medium">${address.country_name || '-'}</span>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <div class="pt-3 mt-3 border-top border-secondary-subtle">
                                        <div class="d-flex justify-content-between flex-wrap small text-muted">
                                            
                                            ${address.company_name 
                                                ? `<div class="text-truncate me-3">
                                                        <i class="bi bi-building me-1"></i> ${address.company_name}
                                                    </div>` 
                                                : ''
                                            }
                                            
                                            ${address.type_of_place 
                                                ? `<div>
                                                        <i class="bi bi-geo-alt me-1"></i> ${address.type_of_place}
                                                    </div>` 
                                                : ''
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
            } else {
                $('.billing-address-edit, .shipping-address-edit').addClass('d-none');
            }
        }
    })
});

$(document).on('click', '.selectable-address', function () {
    var addressType = $(this).closest('#update_billing_address').length ? 'billing' : 'shipping';
    
    $('#update_' + addressType + '_address .selectable-address').removeClass('border-primary bg-primary-subtle').addClass('border-light-subtle bg-white');
    $(this).removeClass('border-light-subtle').addClass('border-primary');
    $(this).removeClass('bg-white').addClass('bg-primary-subtle');
    const addressCity = $(this).data('address-city');
    const addressCountryName = $(this).data('address-country-name');
    const addressCountry = $(this).data('address-country');
    const addressStateName = $(this).data('address-state-name');
    const addressState = $(this).data('address-state');
    const addressZip = $(this).data('address-zip');
    const addressTypeOfPlace = $(this).data('address-type-of-place');
    const addressAddress1 = $(this).data('address-address_1');
    const addressAddress2 = $(this).data('address-address_2');
    const addressCompany = $(this).data('address-company');

    $('.' + addressType + '-address-street').text(addressAddress2 ? `${addressAddress1}, ${addressAddress2}` : addressAddress1);

    $('.' + addressType + '-address-city').text(addressCity);
    $('.' + addressType + '-address-state').text(addressStateName);
    $('.' + addressType + '-address-zip').text(addressZip);
    $('.' + addressType + '-address-country').text(addressCountryName);
    $('.' + addressType + '-address-type-of-place').text('(' + addressTypeOfPlace + ')');

    $('.' + addressType + '-first-name').val($('.billing-address-name').text());
    $('.' + addressType + '-last-name').val('');
    $('.' + addressType + '-company-name').val(addressCompany);
    $('.' + addressType + '-email').val($('.billing-address-email').text());
    $('.' + addressType + '-phone').val($('.billing-address-phone').text());
    $('.' + addressType + '-address-1').val(addressAddress1);
    $('.' + addressType + '-address-2').val(addressAddress2);
    $('.' + addressType + '-country').val(addressCountry);
    $('.' + addressType + '-state').val(addressState);
    $('.' + addressType + '-city').val(addressCity);
    $('.' + addressType + '-zip').val(addressZip);
    $('.' + addressType + '-type-of-place').val(addressTypeOfPlace);

    debouncedCalculateTaxShipping()
});

function isEmptyObject(obj) {
    return obj && Object.keys(obj).length === 0 && obj.constructor === Object;
}

// --------------------- End Handle Address --------------------------

// --------------------- Search Product ------------------------------

function getTaxSelectBox(selectedSlug = '') {
    if (!Array.isArray(taxes)) return '';

    let select = `<select name="product_tax[]" class="form-control select2 tax-select-box" disabled>`;
    select += `<option value="">${jsLang('No Tax')}</option>`;
    taxes.forEach(tax => {
        const selected = (tax.slug === selectedSlug) ? 'selected' : '';
        select += `<option value="${tax.slug}" ${selected}>${tax.name}</option>`;
    });

    select += `</select>`;
    return select;
}

function formatCurrency(amount) {
    if (isNaN(amount)) amount = 0;

    const fixedAmount = parseFloat(amount).toFixed(decimal_digits);
    const [integerPart, decimalPart] = fixedAmount.split('.');

    const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousand_separator);
    const formattedDecimal = hideDecimal && Number(decimalPart) === 0 ? '' : '.' + decimalPart;

    const isNegative = formattedInteger.startsWith('-');
    const cleanInteger = isNegative ? formattedInteger.slice(1) : formattedInteger;

    return isNegative
        ? `-${currencySymbol}${cleanInteger}${formattedDecimal}`
        : `${currencySymbol}${cleanInteger}${formattedDecimal}`;
}

function isReadyToSearchProduct() {
    let errorMessage = '';

    const locationId = $('#location_id').val();
    const vendorId = $('#vendor_id').val();

    if (!vendorId) {
        errorMessage = jsLang('Please select a vendor first');
    } else if (!locationId) {
        errorMessage = jsLang('Please select a location first');
    } 

    if (errorMessage) {
        $('.search-error').text(errorMessage).removeClass('d-none');

        setTimeout(() => {
            $('.search-error').addClass('d-none');
        }, 4000);

        return false;
    } 
    
    $('.search-error').addClass('d-none');

    return true;
}

$(document).on('click', function (e) {
    if (!$(e.target).closest('#productSearch, #productList').length) {
        $('#productList').hide();
        $('.product-search-parent .search-box').removeClass('active');
    }
});

let debounceTimer;
$(document).on('click keyup', '#productSearch', function (e) {
    clearTimeout(debounceTimer);

    if (! isReadyToSearchProduct()) {        
        return false;
    }

    if (e.type === 'click' && $('.product-dropdown').html().trim() != '') {
        $('#productList').show();
        $('.product-search-parent .search-box').addClass('active');

        return false;
    }

    let keyword = $(this).val();
    let vendorId = $('#vendor_id').val();
    let locationId = $('#location_id').val();
    debounceTimer = setTimeout(function () {
        $.ajax({
            url: GLOBAL_URL + '/admin/search-product',
            type: "GET",
            data: {
                keyword: keyword,
                vendor_id: vendorId,
                location_id: locationId,
            },
            dataType: "json",
            beforeSend: function () {
                $('.product-dropdown').html(`
                    <div class="product-item">
                        <div class="product-details py-5 bg-white">
                            <div class="product-title text-center py-5 my-5 f-16">
                                <div class="spinner-border text-secondary" role="status">
                                    <span class="sr-only"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                $('#productList').show();
                $('.product-search-parent .search-box').addClass('active');
            },
            success: function (response) {
                var name = '';
                var image = '';
                var price = 0;
                var isVariable = false;
                $('.product-dropdown').html('');

                response.data?.map(function (product) {
                    name = product.name;
                    image = product.featured_image_small;
                    isVariable = product.type == 'Variable Product' || product.type == 'Variable';
                    price = product.sale_price ? product.sale_price_formatted : product.regular_price_formatted;

                    if (!isVariable) {                        
                        if (product.stock_status == 'Out Of Stock') {
                            return;
                        }

                        var stock = '';
                        if (product.is_show_stock_quantity) {
                            stock = `(${product.stock_quantity} product(s) available)`
                        } else {
                            stock = `(${product.stock_status})`
                        }

                        $('.product-dropdown').append(`
                            <div class="product-item simple">
                                <span class="d-none product-id">${product.id}</span>
                                <span class="d-none stock-quantity">${product.stock_quantity}</span>
                                <span class="d-none tax-class">${product.tax_classes}</span>
                                <span class="d-none unit">${product.unit}</span>
                                <span class="d-none default_unit">${product.default_unit}</span>
                                <img src="${image}" class="product-img" alt="Acer">
                                <div class="product-details">
                                    <div class="product-title">${name}</div>
                                    <div class="product-meta-block">
                                        <div class="product-info">
                                            <span class="product-availibility">${stock}</span> 
                                            <span class="product-price">(${price})</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    } else {
                        var data = '';
                        product.variations.forEach(function (variation) {
                            if (variation.stock_status == 'Out Of Stock') {
                                return;
                            }

                            const price = variation.sale_price 
                                ? variation.sale_price_formatted 
                                : variation.regular_price_formatted;

                            const attributeLabels = {};

                            for (const rawKey in variation.attributes) {
                                const attrKey = rawKey.replace('attribute_', '');
                                const attrValue = variation.attributes[rawKey];

                                if (attrKey === 'color') {
                                    const matchedColor = product.attribute_values?.color?.find(color => color.id == attrValue);
                                    if (matchedColor) {
                                        attributeLabels['Color'] = matchedColor.value;
                                    } else {
                                        attributeLabels['Color'] = attrValue;
                                    }
                                } else {
                                    const matchedAttr = product.attribute_values[attrKey]?.find(attr => attr.id == attrValue);
                                    if (matchedAttr) {
                                        attributeLabels[attrKey.charAt(0).toUpperCase() + attrKey.slice(1)] = matchedAttr.value;
                                    } else {
                                        attributeLabels[attrKey.charAt(0).toUpperCase() + attrKey.slice(1)] = attrValue;
                                    }
                                }
                            }

                            const variationLabel = Object.entries(attributeLabels)
                                .map(([key, value]) => `${key}: ${value}`)
                                .join(', ');
                                
                            var stock = '';
                            if (variation.is_show_stock_quantity) {
                                stock = `(${variation.total_stocks} product(s) available)`
                            } else {
                                stock = `(${variation.stock_status})`
                            }

                            data += `
                                <div class="product-meta-block">
                                    <span class="d-none product-id">${variation.id}</span>
                                    <span class="d-none stock-quantity">${variation.total_stocks}</span>
                                    <span class="d-none tax-class">${variation.tax_classes}</span>
                                    <span class="d-none unit">${product.unit}</span>
                                    <span class="d-none default_unit">${product.default_unit}</span>
                                    <div class="product-info">
                                        <span class="product-variation">(${variationLabel})</span> 
                                        <span class="product-availibility">${stock}</span> 
                                        <span class="product-price">(${price})</span>
                                    </div>
                                </div>
                            `;
                        })
                        $('.product-dropdown').append(`
                            <div class="product-item variation">
                                <img src="${image}" class="product-img" alt="BenQ">
                                <div class="product-details">
                                    <div class="product-title">${name}</div>
                                    ${data}
                                </div>
                            </div>
                        `);
                    }
                })

                $('#productList').show();
                $('.product-search-parent .search-box').addClass('active');

                if ($('.product-dropdown').html() == '') {
                    $('.product-dropdown').html(`
                        <div class="product-item">
                            <div class="product-details">
                                <div class="product-title text-center py-5 bg-white f-16">${jsLang('No product found!')}</div>
                            </div>
                        </div>
                    `);
                }
            }
        })
    }, 300);
});

$(document).on('click', '.product-item.simple, .product-item.variation .product-meta-block', function () {
    var parent = $(this);
    var variation = '';
    if ($(this).closest('.product-item').hasClass('variation')) {
        parent = $(this).closest('.product-item');
        variation = $(this).find('.product-variation').text();
    }

    var image = parent.find('.product-img').attr('src');
    var name = parent.find('.product-title').text();
    var productId = $(this).find('.product-id').text();
    var taxClass = $(this).find('.tax-class').text();
    var productStockQty = $(this).find('.stock-quantity').text();
    var unit = $(this).find('.unit').text();
    unit = unit != 'null' ? unit : $(this).find('.default_unit').text();
    
    
    var price = $(this).find('.product-price').text().replace('($', '').replace(')', '');
    var inputFieldPrice = price.match(/[\d.]+/g)?.join('') || '0';

    // Check if product already exists
    let existingRow = $(`.product-list-tr input.product-id[value="${productId}"]`).closest('tr');

    if (existingRow.length) {
        // Update quantity by +1
        let qtyInput = existingRow.find('.product-qty');
        let maxQty = parseInt(qtyInput.attr('max')) || 1000;
        let currentQty = parseInt(qtyInput.val()) || 0;
        let updatedQty = Math.min(currentQty + 1, maxQty);

        qtyInput.val(updatedQty);
    } else {

        var data = `<tr class="product-list product-list-tr">
            <td>
                <input type="hidden" name="product[id][]" class="product-id" value="${productId}">
                <input type="hidden" name="product[variation_meta][]" class="product-tax-class" value="${variation}">
                <img src="${image}" class="product-img" alt="${jsLang('Product Image')}">
            </td>
            <td>
                <div class="text-start">
                    <div class="product-title">
                        <span>${name}</span>
                        <span class="product-variation d-inline-block me-2">${variation}</span>
                        <textarea name="product[description][]" placeholder="${jsLang('Add product IMEI, Serial number or other information here.')}" class="form-control mt-2 product-description-textarea"></textarea>
                    </div>
                </div>
            </td>
            <td>
                <div class="d-flex justify-content-between justify-content-md-end">
                    <span class="d-md-none">${jsLang('Unit')}</span>
                    <span class="text-end text-md-center d-inline d-md-block w-100">${unit}</span>
                </div>
            </td>
            <td>
                <div>
                    <label class="d-md-none">${jsLang('Quantity')}</label>
                    <input type="text" name="product[quantity][]" min="1" max="${productStockQty != 'null' && productStockQty != 0 ? productStockQty : 1000}" class="form-control product-qty positive-float-number" value="1">
                </div>
            </td>
            <td>
                <div>
                    <label class="d-md-none">${jsLang('Cost')}</label>
                    <input type="text" name="product[price][]" class="form-control positive-float-number product-cost" value="${inputFieldPrice}">
                </div>
            </td>
            <td>
                <div>
                    <label class="d-md-none">${jsLang('Tax')}</label>
                    ${getTaxSelectBox(taxClass)}
                </div>
            </td>
            <td>
                <div class="d-flex justify-content-between justify-content-md-end">
                    <span class="d-md-none">${jsLang('Total Price')}</span>
                    <span class="product-total-price">${formatCurrency(price)}</span>
                </div>
            </td>
            <td class="text-end">
                <div class="product-list-delete">
                    <i class="feather icon-trash cursor-pointer"></i>
                    <span class="d-md-none">Delete</span>
                </div>
            </td>
        </tr>`;

        $('.product-list-tbody').prepend(data);
    }
    calculateTotalAndSubtotal();
    debouncedCalculateTaxShipping();
    calculateDiscount();
    calculateGrandTotal();

    $('.tax-select-box').select2();

    $('#productList').hide();
    $('.product-search-parent .search-box').removeClass('active');
})

$(document).on('click', '.product-list-delete', function () {
    $(this).closest('tr').remove();
    calculateTotalAndSubtotal();
    debouncedCalculateTaxShipping();
    calculateDiscount();
    calculateGrandTotal();
});

function calculateTotalAndSubtotal(returnAmount = false) {
    var subtotal = 0;
    var subTotalSelector = $('.total-summary-area .subtotal');
    var productListTr = $('.product-list.product-list-tr');    

    productListTr.each(function (index, element) {
        var productTotalPrice = $(element).find('.product-qty').val() * $(element).find('.product-cost').val();
        
        $(element).find('.product-total-price').text(formatCurrency(productTotalPrice));
        subtotal += productTotalPrice;
    });
    
    if (returnAmount) {
        return subtotal;
    }

    subTotalSelector.text(formatCurrency(subtotal));
}

$(document).on('input change', 'input[name="product_qty[]"]', function () {
    const min = parseInt($(this).attr('min')) || 1;
    const max = parseInt($(this).attr('max')) || 1000;
    let value = parseInt($(this).val());

    if (isNaN(value) || value < min) {
        $(this).val(min);
    } else if (value > max) {
        $(this).val(max);
    }
});

$(document).on('change input', '.product-qty, .product-cost', function () {
    let $productQty = $(this).closest('tr').find('.product-qty');
    let maxQty = parseFloat($productQty.attr('max')) || 1000;
    let currentQty = parseFloat($productQty.val()) || 0;

    // Clamp value to maxQty
    if (currentQty > maxQty) {
        $productQty.val(maxQty);
    } else if (currentQty <= 0) {
        // Delay fixing empty/zero input
        setTimeout(function () {
            let newQty = parseFloat($productQty.val()) || 0;
            if (newQty <= 0) {
                $productQty.val(1);
            }
        }, 2000);
    }

    calculateTotalAndSubtotal();
    debouncedCalculateTaxShipping();
    calculateDiscount();
    calculateGrandTotal();
})

// Adjustment Fee

$('#adjustmentBtn').on('click', function() {
    $('#adjust_table').append(`
        <tr class="adjustRow">
            <td class="text-center ps-0">
                <input type="text" name="adjustment[name][]" class="inputAdjustName inputFieldDesign form-control text-center">
            </td>
            <td class="text-center">
                <input name="adjustment[amount][]" class="inputAdjustAmount inputFieldDesign form-control text-center positive-float-number" type="text" value="0">
            </td>
            <td class="text-center padding_top_18px">
                <a class="delete-adjustment" href="javascript:void(0)"><i class="feather icon-trash f-16 d-inline-block mt-1 pt-2"></i></a>
            </td>
        </tr>
    `);
});

function calculateAdjustmentFee() {
    let total = 0;
    $('.inputAdjustAmount').each(function() {        
        const val = parseFloat($(this).val());
        total += isNaN(val) ? 0 : val; // Safely add only valid numbers
    });

    $('.adjustment-fee').text(formatCurrency(total));
    $('.adjustment-fee').attr('data-amount', total);
}

$(document).on('click', '.delete-adjustment', function() {
    $(this).closest('tr').remove();
    calculateAdjustmentFee();
    calculateGrandTotal();
});

$(document).on('input change', '.inputAdjustAmount', function() {
    calculateAdjustmentFee();
    calculateGrandTotal();
})

function getAddress()
{
    var country = $('#shipping_country').val();
    var state = $('#shipping_state').val();
    var city = $('#shipping_city').val();
    var zip = $('#shipping_zip').val();

    if (country == '' || state == '' && city == '') {
        country = $('#billing_country').val();
        state = $('#billing_state').val();
        city = $('#billing_city').val();
        zip = $('#billing_zip').val();
    }

    return {
        country: country,
        state: state,
        city: city,
        zip: zip,
        ship_different: 'on'
    }
}

// Invoice add to cart
function calculateTaxShipping(add_to_cart = true) {
    return new Promise((resolve, reject) => {
        let productData = [];

        $('.product-list-tr').each(function () {
            const id = $(this).find('input[name="product[id][]"]').val();
            const qty = $(this).find('input[name="product[quantity][]"]').val();
            const price = $(this).find('input[name="product[price][]"]').val();

            productData.push({
                id: id,
                quantity: parseFloat(qty) || 0,
                price: parseFloat(price) || 0
            });
        });

        $.ajax({
            url: GLOBAL_URL + '/admin/invoice/tax-shipping',
            type: 'GET',
            data: {
                invoice_user_id: $('#random_id').val(),
                products: productData,
                address: getAddress(),
                select: 'all',
                custom_price: true,
                add_to_cart: add_to_cart
            },
            dataType: 'json',
            success: function(response) {
                if (response.tax_shipping.displayTaxTotal == 'as a single total') {
                    $('.tax-title').text(jsLang('Tax'));
                    $('.tax-amount').text(formatCurrency(response.tax_shipping.tax));
                    $('.tax-amount').attr('data-amount', response.tax_shipping.tax);
                    $('.tax-amount').attr('data-title', 'Tax');
                } else {
                    let itemizedHtml = '';
                    let taxTitle = '';
                    let totalTax = 0;

                    for (const [taxName, taxAmount] of Object.entries(response.tax_shipping.tax)) {
                        taxTitle += `<p class="mb-0 fw-normal">${taxName}</p>`;
                        itemizedHtml += `<p class="mb-0 fw-normal itemized-tax" data-amount="${taxAmount}" data-title="${taxName}">${formatCurrency(taxAmount)}</p>`;
                        totalTax += taxAmount;
                    }

                    if (taxTitle != '') {
                        $('.tax-title').html(`<div><p class="mb-2 bb-dashed-gray d-inline-block">${jsLang('Taxes')}</p>${taxTitle}</div>`);
                        $('.tax-amount').html(`<div><p class="mb-20 pb-3"></p>${itemizedHtml}</div>`);
                        $('.tax-amount').attr('data-amount', totalTax);
                    } else {
                        $('.tax-title').text(jsLang('Tax'));
                        $('.tax-amount').text(formatCurrency(0.00));
                        $('.tax-amount').attr('data-amount', 0.00);
                    }
                }

                let emptyShippingColumn = `<p class="col-12 text-center">${jsLang('The shipping fee has not loaded yet.')}</p>`;
                let shippingColumn = '';

                for (const [shippingName, shippingAmount] of Object.entries(response.tax_shipping.shipping)) {
                    emptyShippingColumn = '';
                    let checked = response.tax_shipping.key == shippingName ? 'checked' : '';
                    if (checked == 'checked') {
                        $('.shipping-fee').text(formatCurrency(shippingAmount));
                        $('.shipping-fee').attr('data-amount', shippingAmount);
                    }

                    shippingColumn += `<div class="col-sm-12 mb-3">
                                <div class="mb-2 radio radio-warning d-inline">
                                    <input type="radio" id="${shippingName.replace(' ', '_')}" name="shipping_fee_modal" value="${shippingAmount}"  ${checked}>
                                    <label class="cr custom" for="${shippingName.replace(' ', '_')}">${shippingName} <span class="text-dark"> - ${formatCurrency(shippingAmount)}</span></label>
                                </div>
                            </div>`;
                }

                if (emptyShippingColumn != '') {
                    $('.shipping-fee').text(formatCurrency(0.00));
                    $('.shipping-fee').attr('data-amount', 0);
                    shippingColumn = emptyShippingColumn;
                }

                $('.shipping-fee-row').html(shippingColumn);  

                if (add_to_cart) {
                    $('#applied_coupon').html('');
                    $('.coupon-amount').text(formatCurrency(0));   
                    $('.coupon-amount').attr('data-amount', 0); 
                }
                calculateGrandTotal();
                resolve(response);
            }
        });
    })
}

function taxShippingDebounce(func, delay = 2000) {
    let timer;
    return function (...args) {
        $('.tax-amount').text('...');
        $('.tax-title').text(jsLang('Tax'));
        $('.shipping-fee').text('...');
        $('.shipping-fee').attr('data-amount', 0);
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}

const debouncedCalculateTaxShipping = taxShippingDebounce(calculateTaxShipping);

$(document).on('change', 'input[name="shipping_fee_modal"]', function() {
    $('.shipping-fee').text(formatCurrency($(this).val()));
    $('.shipping-fee').attr('data-amount', $(this).val());
    calculateGrandTotal();
})

$('.discount-type').on('click', function() {
    $('.discount-type').removeClass('active');
    $(this).addClass('active');
})

function calculateDiscount() {
    
    let type = $('.discount-type.active').attr('data-type');
    if (type == 'symbol') {
        let discountValue = $('.discount-amount-input').val();
        $('.discount-amount').text(formatCurrency(discountValue || 0));
        $('.discount-amount').attr('data-amount', discountValue || 0);
    } else {
        let subTotal = calculateTotalAndSubtotal(true);
        let discount = (subTotal * $('.discount-amount-input').val()) / 100;
        $('.discount-amount').text(formatCurrency(discount));
        $('.discount-amount').attr('data-amount', discount);
    }
}

$('.discount-amount-input').on('input', function() {
    calculateDiscount();
    calculateGrandTotal();
})

$('.discount-type').on('click', function() {
    calculateDiscount();
    calculateGrandTotal();
})

function applyCoupon() {
    $.ajax({
        url: GLOBAL_URL + '/check-coupon',
        type: 'POST',
        data: {
            _token: token,
            invoice_user_id: $('#random_id').val(),
            discount_code: $('.coupon-code').val(),
            custom_price: true
        },
        dataType: 'json',
        success: function(response) {
            if (response.status == 0) {
                $('.coupon-error').text(response.message);
                $('.coupon-success').text('');
                setTimeout(() => {
                    $('.coupon-error').text('');
                }, 3000);
            } else {
                $('.coupon-code').val('');
                $('.coupon-error').text('');
                $('.coupon-success').text(jsLang('Coupon applied successfully!'));
                $('#applied_coupon').html('');
                let discount = 0;
                for (const key in response.data) {
                    let calculatedDiscount = Number(response.data[key].calculated_discount);
                    discount += calculatedDiscount;

                    $('#applied_coupon').append(`
                        <div class="d-flex justify-content-between single-coupon">
                            <div class="d-flex justify-content-center">
                                <div class="delete-coupon me-2 cursor-pointer" data-id="${key}">
                                    <i class="feather icon-trash"></i>
                                </div>
                                <p class="text-gray-12 dm-sans font-medium Uppercase text-sm pl-2">${jsLang('Coupon')}: ${response.data[key].code}</p>
                            </div>
                            <p class="text-gray-12 dm-sans font-medium text-sm single-coupon-amount" data-amount="${calculatedDiscount}" data-code="${response.data[key].code}">- ${formatCurrency(calculatedDiscount)}</p>
                        </div>    
                    `)
                }

                $('.coupon-amount').text(formatCurrency(discount));
                $('.coupon-amount').attr('data-amount', discount);

                let emptyShippingColumn = `<p class="col-12 text-center">${jsLang('The shipping fee has not loaded yet.')}</p>`;
                let shippingColumn = '';

                for (const [shippingName, shippingAmount] of Object.entries(response.shipping)) {
                    emptyShippingColumn = '';
                    let checked = 'checked';
                    if (checked == 'checked') {
                        $('.shipping-fee').text(formatCurrency(shippingAmount));
                        $('.shipping-fee').attr('data-amount', shippingAmount);
                    }

                    shippingColumn += `<div class="col-sm-12 mb-3">
                                <div class="mb-2 radio radio-warning d-inline">
                                    <input type="radio" id="${shippingName.replace(' ', '_')}" name="shipping_fee_modal" value="${shippingAmount}"  ${checked}>
                                    <label class="cr custom" for="${shippingName.replace(' ', '_')}">${shippingName} <span class="text-dark"> - ${formatCurrency(shippingAmount)}</span></label>
                                </div>
                            </div>`;
                }

                if (emptyShippingColumn != '') {
                    $('.shipping-fee').text(formatCurrency(0.00));
                    $('.shipping-fee').attr('data-amount', 0);
                    shippingColumn = emptyShippingColumn;
                }

                $('.shipping-fee-row').html(shippingColumn); 

                calculateGrandTotal();
                setTimeout(() => {
                    $('.coupon-success').text('');
                }, 3000);
            }
        }
    })
}

$('.apply-coupon-btn').on('click', function() {
    applyCoupon();
})

$(document).on('click', '.delete-coupon', function() {
    let id = $(this).attr('data-id');
    $(this).closest('.single-coupon').remove();
    $.ajax({
        url: GLOBAL_URL + '/delete-coupon',
        type: 'POST',
        data: {
            _token: token,
            index: id,
            invoice_user_id: $('#random_id').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.status == 1) {
                $('.coupon-amount').text(formatCurrency(response.coupon_amount));
                $('.coupon-amount').attr('data-amount', response.coupon_amount);
                calculateGrandTotal();
            }
        }
    })
})

function calculateGrandTotal() {
    let subTotal = calculateTotalAndSubtotal(true);
    let fee = parseFloat($('.adjustment-fee').attr('data-amount'));
    let shipping = parseFloat($('.shipping-fee').attr('data-amount'));
    let tax = parseFloat($('.tax-amount').attr('data-amount'));
    let discount = parseFloat($('.discount-amount').attr('data-amount') || 0);
    let coupon = parseFloat($('.coupon-amount').attr('data-amount'));
    let grandTotal = subTotal + tax + fee + shipping - discount - coupon;
    
    
    $('.grand-total').text(formatCurrency(grandTotal));
    $('.grand-total').attr('data-amount', grandTotal);
}


$('.clear-shipping-address').on('click', function() {
    $('.shipping-address-name').text('---------------');
    $('.shipping-address-phone').text('---------------');
    $('.shipping-address-email').text('--------------------');
    $('.shipping-address-street').text('-----------------------------');
    $('.shipping-address-city').text('--------------');
    $('.shipping-address-state').text('-------------');
    $('.shipping-address-zip').text('---------------');
    $('.shipping-address-country').text('-----------');
    $('.shipping-address-type-of-place').text('');

    $('#shipping_address_form').find('input').val('');
    $('#shipping_address_form').find('select').val('');

    shippingCountry = null;
    shippingState = null;
    shippingCity = null;

    $('#shipping_city').val('').trigger('change');
    $(this).addClass('d-none');
    calculateGrandTotal();
})

function clearProductArea()
{
    $('.product-list-tr').remove();
    $('.total-summary-area .price').text(formatCurrency(0))
    $('.total-summary-area .price').attr('data-amount', 0)
    $('.tax-title').text(jsLang('Tax'));
}

function getInvoiceData() {
    let vendorId = $('#vendor_id').val();
    let userId = $('#user_id').val();
    let randomId = $('#random_id').val();
    let locationId = $('#location_id').val();
    let orderDate = $('#order_date').val();
    let paymentMethod = $('#payment_method').val();
    let status = $('#status').val();
    let billingAddress = {
        firstName: $('.billing-first-name').val(),
        lastName: $('.billing-last-name').val(),
        companyName: $('.billing-company-name').val(),
        email: $('.billing-email').val(),
        phone: $('.billing-phone').val(),
        address1: $('.billing-address-1').val(),
        address2: $('.billing-address-2').val(),
        country: $('.billing-country').val(),
        state: $('.billing-state').val(),
        city: $('.billing-city').val(),
        zip: $('.billing-zip').val(),
        type_of_place: $('.billing-type-of-place').val(),
    };
    let shippingAddress = {
        firstName: $('.shipping-first-name').val(),
        lastName: $('.shipping-last-name').val(),
        companyName: $('.shipping-company-name').val(),
        email: $('.shipping-email').val(),
        phone: $('.shipping-phone').val(),
        address1: $('.shipping-address-1').val(),
        address2: $('.shipping-address-2').val(),
        country: $('.shipping-country').val(),
        state: $('.shipping-state').val(),
        city: $('.shipping-city').val(),
        zip: $('.shipping-zip').val(),
        type_of_place: $('.shipping-type-of-place').val(),
    };

    let productData = [];

    $('.product-list-tr').each(function () {
        const id = $(this).find('input[name="product[id][]"]').val();
        const qty = $(this).find('input[name="product[quantity][]"]').val();
        const price = $(this).find('input[name="product[price][]"]').val();
        const variation = $(this).find('input[name="product[variation_meta][]"]').val();
        const description = $(this).find('textarea[name="product[description][]"]').val();

        productData.push({
            id: id,
            quantity: parseFloat(qty) || 0,
            price: parseFloat(price) || 0,
            description: description,
            variation_meta: variation
        });
    });

    let subTotal = calculateTotalAndSubtotal(true);
    let shippingAmount = parseFloat($('.shipping-fee').attr('data-amount'));
    let shippingName = $('input[name="shipping_fee_modal"]:checked').attr('id') || oldShippingName;
    let discount = parseFloat($('.discount-amount').attr('data-amount'));
    let coupon = [];
    $('.single-coupon-amount').each(function() {
        const amount = parseFloat($(this).attr('data-amount')) || 0;
        const code = $(this).attr('data-code');
        coupon.push({
            code: code,
            amount: amount
        });
    });


    let tax = [];

    if ($('.tax-amount').find('.itemized-tax').length) {
        $('.itemized-tax').each(function() {
            const name = $(this).attr('data-title');
            const amount = parseFloat($(this).attr('data-amount')) || 0;

            tax.push({
                name: name,
                amount: parseFloat(amount) || 0,
            });
        });
    } else {
        tax.push({
            name: 'Tax',
            amount: parseFloat($('.tax-amount').attr('data-amount')) || 0,
        });
    }

    let fee = [];
    $('.adjustRow').each(function() {
        const name = $(this).find('input[name="adjustment[name][]"]').val();
        const amount = $(this).find('input[name="adjustment[amount][]"]').val();

        fee.push({
            name: name,
            amount: parseFloat(amount) || 0,
        });
    });

    let customerNote = $('textarea[name="customer_note"]').val();

    return {
        _token: token,
        vendorId: vendorId,
        userId: userId,
        randomId: randomId,
        locationId: locationId,
        orderDate: orderDate,
        paymentMethod: paymentMethod,
        status: status,
        billingAddress: billingAddress,
        shippingAddress: shippingAddress,
        productData: productData,
        subTotal: subTotal,
        shippingAmount: shippingAmount,
        shippingName: shippingName,
        discount: discount,
        coupon: coupon,
        fee: fee,
        customerNote: customerNote,
        tax: tax,
        customLocation: true
    };
}

function saveInvoice(data) {
    $.ajax({
        url: GLOBAL_URL + '/admin/invoice/save',
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function() {
            triggerNotification(jsLang('Invoice saving') + '...');
        },
        success: function(response) {
            if (response.status == true) {
                triggerNotification(response.message);
                setTimeout(() => {
                    window.location.href = orderListUrl;
                }, 1000);
            } else {
                toggleButtonState('.save-invoice', false);
                triggerNotification(response.message);
            }
        }
    });
}

function toggleButtonState(selector, disable = true, textWhenDisabled = null) {
    const $btn = $(selector);

    if (disable) {
        $btn.prop('disabled', true);
        if (textWhenDisabled !== null) {
            $btn.data('original-text', $btn.html());
            $btn.html(textWhenDisabled);
        }
    } else {
        $btn.prop('disabled', false);
        const originalText = $btn.data('original-text');
        if (originalText) {
            $btn.html(originalText);
            $btn.removeData('original-text');
        }
    }
}

$('.save-invoice').on('click', function (e) {
    e.preventDefault();

    toggleButtonState('.save-invoice', true);
    const data = getInvoiceData();

    if ($('#invoice-edit-container').length) {
        data.invoiceId = invoiceId;
    }    
    
    if (!data.productData.length) {
        toggleButtonState('.save-invoice', false);
        return triggerNotification(jsLang('Please add product to the invoice.'));
    }

    if ($('.shipping-fee').text() === '...') {
        toggleButtonState('.save-invoice', false);
        return triggerNotification(jsLang('Please wait while tax and shipping are calculated.'));
    }

    if ($('.grand-total').attr('data-amount') < 0) {
        toggleButtonState('.save-invoice', false);
        return triggerNotification(jsLang('Total amount cannot be negative.'));
    }

    saveInvoice(data);
});

if ($('#invoice-edit-container').length) {
    calculateTaxShipping().then(function (response) {
        if (oldShippingName) {
            $('#' + oldShippingName).prop('checked', true).trigger('change');
        }
        $('.inputAdjustAmount').trigger('change');
        for (const coupon of couponRedeem) {
            $('.coupon-code').val(coupon.coupon_code);
            applyCoupon();
        }
        
    });
}
