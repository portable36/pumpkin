<?php

namespace Modules\MenuBuilder\Database\Seeders\versions\v5_0_0;

use Illuminate\Database\Seeder;
use Modules\MenuBuilder\Http\Models\MenuItems;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        addMenuItem('admin', 'OTP Log', [
            'parent' => 'Tools',
            'link' => 'otp-logs',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\OtpLogController@index", "route_name":["otp-logs.index", "otp-logs.detail"]}',
            'sort' => 4,
        ]);

        MenuItems::addRouteOnParams('pos/setup', '3', 'vendor.pos.customer');
        MenuItems::addRouteOnParams('pos/setup', '6', 'vendor.pos.customer');
    }

}
