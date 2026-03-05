<?php

namespace Modules\MenuBuilder\Database\Seeders\versions\v3_1_1;

use Illuminate\Database\Seeder;
use Modules\MenuBuilder\Http\Models\MenuItems;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        addMenuItem('vendor', 'Brands', [
            'icon' => 'fas fa-file',
            'link' => 'all-brands',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\BrandController@index", "route_name":["vendor.brands.index", "vendor.brands.create", "vendor.brands.edit", "vendor.brands.store", "vendor.brands.destroy"]}',
            'sort' => 3.1,
        ]);

        addMenuItem('vendor', 'Attributes', [
            'icon' => 'fas fa-list-alt',
            'link' => 'all-attributes',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\AttributeController@index", "route_name":["vendor.attribute.index", "vendor.attribute.create", "vendor.attribute.edit", "vendor.attribute.store", "vendor.attribute.destroy"]}',
            'sort' => 3.2,
        ]);

        foreach ([1 => 'order.edit', 3 => 'vendorOrder.edit'] as $menu => $route) {
            $orderMenu = MenuItems::where(['link' => 'orders', 'menu' => $menu])->first();
            if (! empty($orderMenu)) {
                $params = $orderMenu->params;

                if (! in_array($route, $params['route_name'])) {
                    $params['route_name'][] = $route;
                    MenuItems::where('id', $orderMenu->id)->update([
                        'params' => $params,
                    ]);
                }
            }
        }

        foreach ([1 => 'refund.edit', 3 => 'vendor.refund.edit'] as $menu => $route) {
            $refundMenu = MenuItems::where(['link' => 'refund-requests', 'menu' => $menu])->first();
            if (! empty($refundMenu)) {
                $params = $refundMenu->params;

                if (! in_array($route, $params['route_name'])) {
                    $params['route_name'][] = $route;
                    MenuItems::where('id', $refundMenu->id)->update([
                        'params' => $params,
                    ]);
                }
            }
        }

        MenuItems::where('link', 'user/list')->where('menu', 1)->update([
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\UserController@index","route_name":["users.index","users.edit","users.pdf","users.csv","users.verify", "users.ledger", "notifications.index", "vendor.customer.addresses", "vendor.customer.addresses.create", "vendor.customer.addresses.edit"]}',
        ]);

        MenuItems::where('link', 'customer')->whereIn('menu', [3, 6])->update([
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\Vendor\\\\CustomerController@index", "route_name":["vendor.customer", "vendor.customer.create", "vendor.customer.edit", "vendor.customer.ledger", "vendor.customer.payment", "vendor.customer.paymentStore"]}',
        ]);

        addMenuItem('admin', 'Customers', [
            'link' => 'customers',
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\CustomerController@index", "route_name":["customers.index", "customers.create", "customers.edit", "customer.addresses.index", "customer.addresses.create", "customer.addresses.edit", "customer.ledger", "customer.payment", "customer.paymentStore"]}',
            'parent' => 'Vendors',
            'menu' => 1,
            'sort' => 20.001,
        ]);

        MenuItems::where([
            'link' => 'general-setting',
            'menu' => 1,
        ])->update([
            'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\CompanySettingController@index","route_name":["preferences.index", "companyDetails.setting", "maintenance.enable", "language.translation", "language.index", "currency.convert", "withdrawalSetting.index", "external-codes.index", "address.setting.index", "language.import", "api-keys.index", "api-settings", "units.index"]}',
        ]);
    }
}
