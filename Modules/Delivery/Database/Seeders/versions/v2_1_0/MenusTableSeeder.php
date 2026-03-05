<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\MenuBuilder\Http\Models\AdminMenus;

class MenusTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        if (! DB::table('menus')->where('name', 'carrier')->first()) {
            DB::table('menus')->insert([
                'id' => 5,
                'name' => 'carrier',
            ]);

            AdminMenus::forgetCache();
        }
    }
}
