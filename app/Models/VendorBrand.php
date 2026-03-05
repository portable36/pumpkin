<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorBrand extends Model
{

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id');
    }

    public static function store($response = [])
    {
        $data['brand_id'] = $response['brand_id'];
        $data['vendor_id'] = auth()->user()->vendor()->vendor_id;
        $data['shop_id'] = isset(auth()->user()->vendor()->vendor->shop) ? auth()->user()->vendor()->vendor->shop->id : null;

        return parent::insertGetId($data);
    }

    public static function destroy($brandId)
    {
        $vendorId = auth()->user()->vendor()->vendor_id;
        $vendorBrand = parent::where('brand_id', $brandId)->where('vendor_id', $vendorId)->first();

        if (! empty($vendorBrand)) {
            $vendorBrand->delete();

            return true;
        }

        return false;
    }
    
}
