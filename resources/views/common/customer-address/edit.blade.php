<div class="row">
    <div class="col-sm-6">
    
        <div class="form-group row">
            <label for="company_name"
                class="control-label ltr:ps-3 rtl:pe-3">{{ __('Company Name') }}</label>
            <div class="col-sm-12">
                <input type="text"
                    class="form-control form-width inputFieldDesign bg-white" id="company_name"
                    name="company_name" value="{{ old('company_name', $address->company_name ?? '') }}"
                    placeholder="{{ __('Company Name') }}"
                    >
            </div>
        </div>

        <div class="form-group row">
            <label for="address_2" class="control-label ltr:ps-3 rtl:pe-3">{{ __('Street Address 2') }}
            </label>
            <div class="col-sm-12">
                <input type="text" placeholder="{{ __('Street Address 2') }}"
                    class="form-control form-width inputFieldDesign" id="address_2"
                    name="address_2" value="{{ old('address_2', $address->address_2 ?? '') }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="state" class="control-label ltr:ps-3 rtl:pe-3">{{ __('State') . ' / ' . __('Province') }}
            </label>
            <div class="col-sm-12">
                <select class="form-control select2 sl_common_bx" name="state" id="state">
                    @foreach ($states as $state)
                        <option value="{{ $state->code }}" data-state={{ $state->code }}> {{ $state->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="type_of_place" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Select the type of your place') }}
            </label>
            <div class="col-sm-12">
                <select class="form-control select2 sl_common_bx" name="type_of_place" id="type_of_place" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                    <option value="home" {{ !empty($address) && $address->type_of_place == 'home' ? 'selected' : '' }}> {{ __('Home') }} </option>
                    <option value="office" {{ !empty($address) && $address->type_of_place == 'office' ? 'selected' : '' }}> {{ __('Office') }} </option>
                </select>
            </div>
        </div>

    </div>

    <div class="col-sm-6">

        <div class="form-group row">
            <label for="address_1" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Street Address 1') }}
            </label>
            <div class="col-sm-12">
                <input type="text" placeholder="{{ __('Street Address 1') }}"
                    class="form-control form-width inputFieldDesign" id="address_1"
                    name="address_1" required minlength="3" value="{{ old('address_1', $address->address_1 ?? '') }}"
                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Street Address 1'), 'y' => 3]) }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="country" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Country') }}
            </label>
            <div class="col-sm-12">
                <select class="form-control select2 sl_common_bx" name="country" id="country" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                    @foreach ($countries as $country)
                        <option data-country="{{ $country->code }}" value="{{ $country->code }}"
                            {{ 
                                $country->code == old('country') || 
                                (!empty($address) && $country->code == $address->country) ? 'selected' : '' }} 
                        >
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="city" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('City') }}
            </label>
            <div class="col-sm-12">
                <select class="form-control select2 sl_common_bx" name="city" id="city" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                    @foreach ($cities as $city)
                        <option value="{{ $city->name }}"
                            
                            {{ $city->name == old('city') || (!empty($address) && $city->name == $address->city) ? 'selected' : '' }}
                        >{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="zip" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Postcode / ZIP') }}
            </label>
            <div class="col-sm-12">
                <input type="text" placeholder="{{ __('Postcode / ZIP') }}"
                    class="form-control form-width inputFieldDesign" id="zip"
                    name="zip" required minlength="3" value="{{ old('zip', $address->zip ?? '') }}"
                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Postcode / ZIP'), 'y' => 3]) }}">
            </div>
        </div>

    </div>
</div>