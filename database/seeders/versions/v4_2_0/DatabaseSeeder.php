<?php

namespace Database\Seeders\versions\v4_2_0;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PreferencesTableSeeder::class,
            EmailTemplatesTableSeeder::class,
            PermissionsTableSeeder::class,
            VendorsTableSeeder::class,
            PermissionUpdateTableSeeder::class,
        ]);

    }
}
