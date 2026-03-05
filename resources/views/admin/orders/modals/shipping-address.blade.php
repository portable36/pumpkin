{{-- Shipping Address Modal --}}
<div id="update_shipping_address" class="modal fade display_none" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Shipping Address') }}</h4>
                <a type="button" class="close h5" data-bs-dismiss="modal">×</a>
            </div>
            <form class="w-100" action="#" method="post" id="shipping_address_form">
                @csrf
                <input type="hidden" class="shipping-first-name" value="">
                <input type="hidden" class="shipping-last-name" value="">
                <input type="hidden" class="shipping-company-name" value="">
                <input type="hidden" class="shipping-email" value="">
                <input type="hidden" class="shipping-phone" value="">
                <input type="hidden" class="shipping-address-1" value="">
                <input type="hidden" class="shipping-address-2" value="">
                <input type="hidden" class="shipping-country" value="">
                <input type="hidden" class="shipping-state" value="">
                <input type="hidden" class="shipping-city" value="">
                <input type="hidden" class="shipping-zip" value="">
                <input type="hidden" class="shipping-type-of-place" value="">
            </form>
            {{-- Address list book --}}
            <div class="modal-body">
                <div class="address-book-list">
                    <div class="row" id="shipping_address_list">
                        @foreach($allAddresses ?? [] as $address)
                            @php
                                $isDefault = $address['is_default'];
                                $cardBorderColor = $address['country'] == $shippingAddress->country && $address['state'] == $shippingAddress->state && $address['city'] == $shippingAddress->city && $address['zip'] == $shippingAddress->zip && $address['type_of_place'] == $shippingAddress->type_of_place && $address['address_1'] == $shippingAddress->address_1 && $address['address_2'] == $shippingAddress->address_2 ? 'border-primary' : 'border-light-subtle';
                                $cardBgColor = $isDefault ? 'bg-primary-subtle' : 'bg-white';
                            @endphp

                            <div class="col-md-6 mb-4">
                                <div 
                                    class="card address-card h-100 border selectable-address {{ $cardBorderColor }} {{ $cardBgColor }} shadow-sm shipping"
                                    data-address-id="{{ $address['id'] }}"
                                    data-address-city="{{ $address['city'] ?? '' }}"
                                    data-address-state="{{ $address['state'] ?? '' }}"
                                    data-address-state-name="{{ $address['state_name'] ?? '' }}"
                                    data-address-country="{{ $address['country'] ?? '' }}"
                                    data-address-country-name="{{ $address['country_name'] ?? '' }}"
                                    data-address-zip="{{ $address['zip'] ?? '' }}"
                                    data-address-type-of-place="{{ $address['type_of_place'] ?? '' }}"
                                    data-address-address_1="{{ $address['address_1'] ?? '' }}"
                                    data-address-address_2="{{ $address['address_2'] ?? '' }}"
                                    data-address-company="{{ $address['company_name'] ?? '' }}"
                                    style="border-radius: 0.75rem; transition: all 0.2s ease-in-out; cursor: pointer;"
                                    onmouseover="this.classList.add('shadow-lg')"
                                    onmouseout="this.classList.remove('shadow-lg')"
                                >
                                    <div class="card-body position-relative p-4 d-flex flex-column">

                                        {{-- Default badge --}}
                                        @if($isDefault)
                                            <span class="badge rounded-pill text-bg-primary position-absolute top-0 end-0 mt-3 me-3 px-3 py-2 fw-semibold shadow-sm">
                                                {{ __('Default') }}
                                            </span>
                                        @endif

                                        {{-- Address lines --}}
                                        <div class="mb-4">
                                            <p class="fs-6 fw-bold text-dark mb-1 me-5">
                                                {{ $address['address_1'] ?? __('Address Not Set') }}
                                            </p>
                                            @if(!empty($address['address_2']))
                                                <p class="text-muted mb-0 me-5 small">{{ $address['address_2'] }}</p>
                                            @endif
                                        </div>

                                        {{-- City, Zip, Country --}}
                                        <div class="flex-grow-1">
                                            <div class="row g-2 text-dark">
                                                <div class="col-6">
                                                    <small class="text-secondary d-block">{{ __('City / State') }}</small>
                                                    <span class="d-block fw-medium">{{ $address['city'] ?? '-' }}, {{ $address['state_name'] ?? '-' }}</span>
                                                </div>

                                                <div class="col-6">
                                                    <small class="text-secondary d-block">{{ __('Zip Code') }}</small>
                                                    <span class="d-block fw-medium">{{ $address['zip'] ?? '-' }}</span>
                                                </div>

                                                <div class="col-12 mt-3">
                                                    <small class="text-secondary d-block">{{ __('Country') }}</small>
                                                    <span class="d-block fw-medium">{{ $address['country_name'] ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="pt-3 mt-3 border-top border-secondary-subtle">
                                            <div class="d-flex justify-content-between flex-wrap small text-muted">
                                                @if(!empty($address['company_name']))
                                                    <div class="text-truncate me-3">
                                                        <i class="bi bi-building me-1"></i> {{ $address['company_name'] }}
                                                    </div>
                                                @endif

                                                @if(!empty($address['type_of_place']))
                                                    <div>
                                                        <i class="bi bi-geo-alt me-1"></i> {{ $address['type_of_place'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
