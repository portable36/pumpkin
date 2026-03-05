<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use App\Models\OrderStatus;
use App\Models\OrderStatusRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $updateData = [
            'Completed' => 7,
            'On hold' => 8,
            'Cancelled' => 9,
            'Refunded' => 10,
        ];

        $deliveryRole = DB::table('roles')->where('slug', 'delivery-man')->first();
        $vendorRole   = DB::table('roles')->where('slug', 'vendor-admin')->first();

        foreach ($updateData as $orderStatusName => $orderByValue) {
            DB::table('order_statuses')->where('name', $orderStatusName)->update(['order_by' => $orderByValue]);
        }

        if (! DB::table('order_statuses')->where('slug', 'assigned')->first()) {
            $orderStatusId = OrderStatus::insertGetId([
                'name' => 'Assigned',
                'slug' => 'assigned',
                'color' => '#30b051',
                'payment_scenario' => 'paid',
                'is_default' => 0,
                'order_by' => 4,
            ]);

            OrderStatusRole::insert([
                [
                    'order_status_id' => $orderStatusId,
                    'role_id' => 1,
                ], [
                    'order_status_id' => $orderStatusId,
                    'role_id' => $deliveryRole?->id,
                ], [
                    'order_status_id' => $orderStatusId,
                    'role_id' => $vendorRole?->id,
                ],
            ]);
        }

        if (! DB::table('order_statuses')->where('slug', 'pickup')->first()) {
            $orderStatusId = OrderStatus::insertGetId([
                'name' => 'Pickup',
                'slug' => 'pickup',
                'color' => '#30b051',
                'payment_scenario' => 'paid',
                'is_default' => 0,
                'order_by' => 5,
            ]);

            OrderStatusRole::insert([
                [
                    'order_status_id' => $orderStatusId,
                    'role_id' => 1,
                ], [
                    'order_status_id' => $orderStatusId,
                    'role_id' => $deliveryRole?->id,
                ], [
                    'order_status_id' => $orderStatusId,
                    'role_id' => $deliveryRole?->id,
                ],
            ]);
        }

        if (! DB::table('order_statuses')->where('slug', 'delivered')->first()) {
            $orderStatusId = OrderStatus::insertGetId([
                'name' => 'Delivered',
                'slug' => 'delivered',
                'color' => '#30b051',
                'payment_scenario' => 'paid',
                'is_default' => 0,
                'order_by' => 6,
            ]);

            OrderStatusRole::insert([
                [
                    'order_status_id' => $orderStatusId,
                    'role_id' => 1,
                ], [
                    'order_status_id' => $orderStatusId,
                    'role_id' => $deliveryRole?->id,
                ], [
                    'order_status_id' => $orderStatusId,
                    'role_id' => $vendorRole?->id,
                ],
            ]);
        }
    }
}
