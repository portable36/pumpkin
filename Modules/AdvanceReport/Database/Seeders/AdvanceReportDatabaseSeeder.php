<?php

namespace Modules\AdvanceReport\Database\Seeders;

use Illuminate\Database\Seeder;

class AdvanceReportDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(MenuItemsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
    }
}
