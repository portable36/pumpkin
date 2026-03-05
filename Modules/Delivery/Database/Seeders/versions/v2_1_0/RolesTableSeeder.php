<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! \DB::table('roles')->where('slug', 'delivery-man')->first()) {
            \DB::table('roles')->insert([
                0 => [
                    'name' => 'Delivery Man',
                    'slug' => 'delivery-man',
                    'type' => 'global',
                    'description' => 'Delivery man description',
                ],
            ]);
        }
    }
}
