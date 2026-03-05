<?php

namespace App\Services;

class CustomerService
{
    public function getCustomerAddressData($address)
    {
        $country = ! empty($address) ? \Modules\GeoLocale\Entities\Country::where('code', $address->country)->first() : null;

        $state = ! empty($address) && ! empty($country) ? \Modules\GeoLocale\Entities\Division::where(['code' => $address->state, 'country_id' => $country->id])->first() : null;

        $countries = \Modules\GeoLocale\Entities\Country::select('id', 'name', 'code')->orderBy('name')->get();

        if (! empty($country) && ! empty($state)) {
            $cities = \Modules\GeoLocale\Entities\City::where('country_id', $country->id)->where('division_id', $state->id)->get();
        } elseif (! empty($country)) {
            $cities = \Modules\GeoLocale\Entities\City::where('country_id', $country->id)->get();
        } else {
            $cities = [];
        }

        $states = empty($country) ? [] : $country->divisions()->get();

        return [
            'address'   => $address,
            'countries' => $countries,
            'states'    => $states,
            'cities'    => $cities,
        ];
    }
}
