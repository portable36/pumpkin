<?php

namespace Database\Seeders\versions\v4_1_0;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::insertOrIgnore([
            'name' => 'Each',
            'abbr' => 'ea',
            'default' => 'Yes',
            'status' => 'Active',
        ]);
    }
}
