<?php

namespace Database\Seeders\versions\v3_3_0;

use App\Models\Preference;
use App\Models\Vendor;
use App\Models\VendorAssociatedUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssociateUserTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        if (DB::table('preferences')->where('category', 'vendor')->where('field', 'is_associate_user')->exists()) {
            return;
        }

        Preference::updateOrInsert(
            [
                'category' => 'vendor',
                'field' => 'is_associate_user',
            ],
            [
                'value' => '1',
            ]
        );

        $vendors = Vendor::where('status', 'Active')->get();

        $data = [];

        foreach ($vendors as $vendor) {
            $vendorId = $vendor->id;
            $userIds  = DB::table('vendors')
                ->join('order_details', 'vendors.id', '=', 'order_details.vendor_id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('vendors.id', $vendorId)
                ->select('users.id')
                ->groupBy('users.id')
                ->pluck('users.id');

            foreach ($userIds as $userId) {
                $data[] = [
                    'user_id' => $userId,
                    'vendor_id' => $vendorId,
                ];
            }
        }

        VendorAssociatedUser::insert($data);
    }
}
