<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use App\Models\Preference;
use Illuminate\Database\Seeder;

class PreferencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Preference::insertOrIgnore([
            [
                'category' => 'delivery_setting',
                'field' => 'vendor_can_assign_delivery_man',
                'value' => '0',
            ],
            [
                'category' => 'delivery_setting',
                'field' => 'payment_type_delivery_man',
                'value' => 'salary',
            ],
            [
                'category' => 'delivery_setting',
                'field' => 'notification_type_delivery_man',
                'value' => 'none',
            ],
            [
                'category' => 'preference',
                'field' => 'delivery_default_signup_status',
                'value' => 'Pending',
            ],
            [
                'category' => 'preference',
                'field' => 'delivery_signup',
                'value' => '1',
            ],
        ]);
    }
}
