<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Modules\GeoLocale\Entities\Division;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;
    use ModelTrait;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function country1()
    {
        return $this->belongsTo(\Modules\GeoLocale\Entities\Country::class, 'country', 'code');
    }

    /**
     * relation with division
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function states()
    {
        return $this->belongsTo(Division::class, 'state', 'code');
    }

    /**
     * Foreign key with Division model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function geoLocalState()
    {
        return $this->belongsTo('Modules\GeoLocale\Entities\Division', 'state', 'code')->where('country_id', optional($this->geoLocalCountry)->id);
    }

    /**
     * Foreign key with Country model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function geoLocalCountry()
    {
        return $this->belongsTo('Modules\GeoLocale\Entities\Country', 'country', 'code');
    }

    /**
     * Store a new customer address.
     *
     * @param  array  $data
     * @return bool
     */
    public function storeCustomerAddress($data)
    {
        $this->customer_id = $data['customer_id'];
        $this->vendor_id = $data['vendor_id'];
        $this->company_name = $data['company_name'] ?? null;
        $this->address_1 = $data['address_1'];
        $this->address_2 = $data['address_2'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->type_of_place = $data['type_of_place'];
        $this->country = $data['country'];
        $this->city = $data['city'];
        $this->zip = $data['zip'];
        $this->is_default = $data['is_default'] ?? 0;

        return $this->save();
    }

    /**
     * Update customer address.
     *
     * @param  array  $data
     * @param  self  $address
     * @return bool
     */
    public function updateCustomerAddress($data, $address)
    {
        // If $address is null, create a new instance
        if (is_null($address)) {
            $address = new self();
            $address->customer_id = $data['customer_id'] ?? null;
            $address->is_default = 1;
        }

        $vendorID = $data['vendor_id']
            ?? ($address->vendor_id ?? (session('vendorId') ?? (optional(auth()->user())->vendor->vendor_id ?? null)));
        $address->vendor_id = $vendorID;
        $address->customer_id = $data['customer_id'] ?? $address->customer_id ?? null;
        $address->company_name = $data['company_name'] ?? null;
        $address->address_1 = $data['address_1'];
        $address->address_2 = $data['address_2'] ?? null;
        $address->state = $data['state'] ?? null;
        $address->type_of_place = $data['type_of_place'];
        $address->country = $data['country'];
        $address->city = $data['city'];
        $address->zip = $data['zip'];
        $address->is_default = $data['is_default'] ?? $address->is_default ?? 0;

        return $address->save();
    }
}
