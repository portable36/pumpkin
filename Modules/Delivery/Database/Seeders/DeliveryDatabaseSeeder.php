<?php

namespace Modules\Delivery\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Delivery\Database\Seeders\versions\v2_1_0\{
    EmailTemplatesTableSeeder,
    MenuItemsTableSeeder,
    MenusTableSeeder,
    OrderStatusesTableSeeder,
    PermissionRolesTableSeeder,
    PermissionsTableSeeder,
    PreferencesTableSeeder,
    RolesTableSeeder
};

class DeliveryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MenusTableSeeder::class,
            RolesTableSeeder::class,
            OrderStatusesTableSeeder::class,
            PreferencesTableSeeder::class,
            MenuItemsTableSeeder::class,
            EmailTemplatesTableSeeder::class,
            PermissionRolesTableSeeder::class,
            PermissionsTableSeeder::class,
        ]);
    }
}
