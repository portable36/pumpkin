<?php

namespace Database\Seeders\versions\v4_0_0;

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
        ]);

    }
}
