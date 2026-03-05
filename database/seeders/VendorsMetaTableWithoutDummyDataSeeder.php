<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VendorsMetaTableWithoutDummyDataSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('vendors_meta')->delete();

        \DB::table('vendors_meta')->insert([
            0 => [
                'id' => 1,
                'owner_type' => 'App\\Models\\Vendor',
                'owner_id' => 1,
                'type' => 'string',
                'key' => 'description',
                'value' => 'Welcome to Online Gizmo Tizmo Bangladesh.Buy Game Currency,In-Game Recharge,Digital Goods at cheap rate in Bangladesh from OGSBD.',
            ],
        ]);

    }
}
