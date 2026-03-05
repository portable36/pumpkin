<?php

namespace Database\Seeders\versions\v4_2_0;

use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorsTableSeeder extends Seeder
{
    public function run(): void
    {
        $existingVendorIds = Customer::pluck('vendor_id')->toArray();
        $vendorsToCreate = Vendor::whereNotIn('id', $existingVendorIds)->get();

        $customers = [];
        foreach ($vendorsToCreate as $vendor) {
            $customers[] = [
                'name' => 'Walking Customer',
                'email' => 'walkingcustomer@gmail.com',
                'phone' => '00000000000' . $vendor->id,
                'password' => bcrypt('123456'),
                'vendor_id' => $vendor->id,
                'status' => 'Active',
            ];
        }

        if (! empty($customers)) {
            Customer::insert($customers);
        }
    }
}
