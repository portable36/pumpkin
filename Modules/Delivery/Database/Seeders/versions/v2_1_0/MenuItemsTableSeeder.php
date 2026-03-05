<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $pickedUpId = OrderStatus::where('slug', 'pickup')->first()?->id;
        $deliveredId = OrderStatus::where('slug', 'delivered')->first()?->id;
        $completedId = OrderStatus::where('slug', 'completed')->first()?->id;


        $parentId = addMenuItem('admin', 'Delivery Boy', [
            'icon' => 'fa fa-truck',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Dashboard', [
            'link' => 'dashboard',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\DashboardController@index","route_name":["carrier.dashboard"]}',
            'icon' => 'fa fa-home',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Assigned', [
            'link' => 'order/assign',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\OrderController@assign","route_name":["carrier.assign"]}',
            'icon' => 'fa fa-list',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Pickup', [
            'link' => 'order/pickup?order_status_id=' . $pickedUpId,
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\OrderController@pickup","route_name":["carrier.pickup"]}',
            'icon' => 'fa fa-truck',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Delivered', [
            'link' => 'order/delivered?order_status_id=' . $deliveredId,
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\OrderController@delivered","route_name":["carrier.delivered"]}',
            'icon' => 'fa fa-check-square',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Completed', [
            'link' => 'order/completed?order_status_id=' . $completedId,
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\OrderController@completed","route_name":["carrier.completed"]}',
            'icon' => 'fa fa-check-square',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Earnings', [
            'link' => 'earning',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\ProfileController@earning","route_name":["carrier.earning"]}',
            'icon' => 'fa fa-th-large',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Withdrawal', [
            'link' => 'withdrawal',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\WithdrawalController@index","route_name":["carrier.withdrawal", "carrier.withdrawal_setting", "carrier.withdrawal_money"]}',
            'icon' => 'fas fa-dollar-sign',
            'sort' => 27,
        ]);

        addMenuItem('carrier', 'Profile', [
            'link' => 'profile',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\ProfileController@create","route_name":["carrier.profile"]}',
            'icon' => 'fas fa-users',
            'sort' => 27,
        ]);

        addMenuItem('admin', 'Add Delivery Boy', [
            'link' => 'delivery/carrier/create',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\DeliveryController@create","route_name":["admin.delivery.carrier.create"]}',
            'sort' => 27,
            'parent' => $parentId,
        ]);

        addMenuItem('admin', 'All Delivery Boy', [
            'link' => 'delivery/carrier',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\DeliveryController@viewAll","route_name":["admin.delivery.carrier.index", "admin.delivery.carrier.edit"]}',
            'sort' => 28,
            'parent' => $parentId,
        ]);

        addMenuItem('admin', 'Withdrawals', [
            'link' => 'delivery/withdrawal',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\WithdrawalController@index","route_name":["admin.delivery.withdrawal.index", "admin.delivery.withdrawal.edit", "admin.delivery.withdrawal.update"]}',
            'sort' => 29,
            'parent' => $parentId,
        ]);

        addMenuItem('admin', 'Settings', [
            'link' => 'delivery/settings',
            'params' => '{"permission":"Modules\\\\Delivery\\\\Http\\\\Controllers\\\\Carrier\\\\DeliveryController@settings","route_name":["admin.delivery.settings"]}',
            'sort' => 31,
            'parent' => $parentId,
        ]);
    }
}
