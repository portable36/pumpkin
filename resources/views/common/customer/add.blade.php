<input type="hidden" name="password" value="C@1{{ str()->random(10) }}">
<input type="hidden" name="status" value="Active">

<div class="row">
    @if (isset($panel) && $panel == 'admin')
        <div class="col-sm-6">
            <div class="form-group row">
                <label for="vendor" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Vendor') }}
                </label>
                <div class="col-sm-12">
                    <div>
                        <select class="form-control select2 sl_common_bx" name="vendor_id" id="vendor_id" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                        <option value="">{{ __('Select one') }}</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}"
                                {{ $vendor->id == old('vendor_id') ? 'selected' : '' }} >{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="col-sm-6">
        <div class="form-group row">
            <label for="name" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Name') }}
            </label>
            <div class="col-sm-12">
                <input type="text" placeholder="{{ __('Name') }}"
                    class="form-control form-width inputFieldDesign" id="name"
                    name="name" required minlength="3" value="{{ old('name') }}"
                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Name'), 'y' => 3]) }}">
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row">
            <label for="email" class="control-label ltr:ps-3 rtl:pe-3">{{ __('Email') }}</label>
            <div class="col-sm-12">
                <input type="email" class="form-control form-width inputFieldDesign bg-white" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" data-type-mismatch="{{ __('Enter a valid :x.', ['x' => strtolower(__('Email'))]) }}" readonly onfocus="this.removeAttribute('readonly');">
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row phone-area">
            <label for="phone" class="col-sm-2 col-form-label require">{{ __('Phone') }}</label>
            <div class="col-sm-12">
                <input type="phone" class="form-control form-width inputFieldDesign" 
                    id="phone" name="phone"
                    value="{{ old('phone') }}"
                    required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row">
            <label for="company_name"
                class="control-label ltr:ps-3 rtl:pe-3">{{ __('Company Name') }}</label>
            <div class="col-sm-12">
                <input type="text"
                    class="form-control form-width inputFieldDesign bg-white" id="company_name"
                    name="company_name" value="{{ old('company_name') }}"
                    placeholder="{{ __('Company Name') }}"
                    >
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
                    name="address_1" required minlength="3" value="{{ old('address_1') }}"
                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Street Address 1'), 'y' => 3]) }}">
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row">
            <label for="address_2" class="control-label ltr:ps-3 rtl:pe-3">{{ __('Street Address 2') }}
            </label>
            <div class="col-sm-12">
                <input type="text" placeholder="{{ __('Street Address 2') }}"
                    class="form-control form-width inputFieldDesign" id="address_2"
                    name="address_2" value="{{ old('address_2') }}">
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row">
            <label for="country" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Country') }}
            </label>
            <div class="col-sm-12">
                <div>
                    <select class="form-control select2 sl_common_bx" name="country" id="country" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                    <option value="">{{ __('Select one') }}</option>
                    @foreach ($countries as $country)
                        <option data-country="{{ $country->code }}" value="{{ $country->code }}"
                            {{ $country->code == old('country') ? 'selected' : '' }} >{{ $country->name }}</option>
                    @endforeach
                </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row">
            <label for="state" class="control-label ltr:ps-3 rtl:pe-3">{{ __('State') . ' / ' . __('Province') }}
            </label>
            <div class="col-sm-12">
                <select class="form-control select2 sl_common_bx" name="state" id="state">
                    <option value=""> {{ __('Select Country First') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row">
            <label for="city" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('City') }}
            </label>
            <div class="col-sm-12">
                <select class="form-control select2 sl_common_bx" name="city" id="city" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                    <option value=""> {{ __('Select State / Province First') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-sm-6"> 
        <div class="form-group row">
            <label for="zip" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Postcode / ZIP') }}
            </label>
            <div class="col-sm-12">
                <input type="text" placeholder="{{ __('Postcode / ZIP') }}"
                    class="form-control form-width inputFieldDesign" id="zip"
                    name="zip" required minlength="3" value="{{ old('zip') }}"
                    oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                    data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Postcode / ZIP'), 'y' => 3]) }}">
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group row">
            <label for="type_of_place" class="control-label require ltr:ps-3 rtl:pe-3">{{ __('Select the type of your place') }}
            </label>
            <div class="col-sm-12">
                <select class="form-control select2 sl_common_bx" name="type_of_place" id="type_of_place" required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                    <option value="home"> {{ __('Home') }} </option>
                    <option value="office"> {{ __('Office') }} </option>
                </select>
            </div>
        </div>
    </div>
</div>