<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorAttribute extends Model
{
    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute', 'attribute_id', 'id');
    }

    public static function store($response = [])
    {
        $data['attribute_id'] = $response['attribute_id'];
        $data['vendor_id'] = auth()->user()->vendor()->vendor_id;
        $data['shop_id'] = isset(auth()->user()->vendor()->vendor->shop) ? auth()->user()->vendor()->vendor->shop->id : null;

        return parent::insertGetId($data);
    }

    public static function destroy($attributeId)
    {
        $vendorId = auth()->user()->vendor()->vendor_id;
        $vendorAttribute = parent::where('attribute_id', $attributeId)->where('vendor_id', $vendorId)->first();

        if (! empty($vendorAttribute)) {
            $vendorAttribute->delete();

            return true;
        }

        return false;
    }
}
