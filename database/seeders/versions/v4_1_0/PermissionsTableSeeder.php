<?php

namespace Database\Seeders\versions\v4_1_0;

use App\Models\Permission;
use App\Models\PermissionRole;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@findCustomerByVendor')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@findCustomerByVendor',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'findCustomerByVendor',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@findLocation')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@findLocation',
                'controller_path' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController',
                'controller_name' => 'PurchaseController',
                'method_name' => 'findLocation',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\AdminOrderController@invoiceTaxShipping')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\AdminOrderController@invoiceTaxShipping',
                'controller_path' => 'App\\Http\\Controllers\\AdminOrderController',
                'controller_name' => 'AdminOrderController',
                'method_name' => 'invoiceTaxShipping',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\VendorOrderController@userAddress')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\VendorOrderController@userAddress',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\VendorOrderController',
                'controller_name' => 'VendorOrderController',
                'method_name' => 'userAddress',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\ProductController@search')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\ProductController@search',
                'controller_path' => 'App\\Http\\Controllers\\ProductController',
                'controller_name' => 'ProductController',
                'method_name' => 'search',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\AdminOrderController@invoiceSave')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\AdminOrderController@invoiceSave',
                'controller_path' => 'App\\Http\\Controllers\\AdminOrderController',
                'controller_name' => 'AdminOrderController',
                'method_name' => 'invoiceSave',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\AdminOrderController@partialPayment')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\AdminOrderController@partialPayment',
                'controller_path' => 'App\\Http\\Controllers\\AdminOrderController',
                'controller_name' => 'AdminOrderController',
                'method_name' => 'partialPayment',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\VendorOrderController@edit')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\VendorOrderController@edit',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\VendorOrderController',
                'controller_name' => 'VendorOrderController',
                'method_name' => 'edit',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! $permission = Permission::where('name', 'App\\Http\\Controllers\\AdminOrderController@update')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\AdminOrderController@update',
                'controller_path' => 'App\\Http\\Controllers\\AdminOrderController',
                'controller_name' => 'AdminOrderController',
                'method_name' => 'update',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        } elseif (! PermissionRole::where('permission_id', $permission->id)->where('role_id', 2)->first()) {
            PermissionRole::insert([
                'permission_id' => $permission->id,
                'role_id' => 2,
            ]);
        }

        $chatPermissions = [
            'Modules\\Ticket\\Http\\Controllers\\ChatController@getConversations',
            'Modules\\Ticket\\Http\\Controllers\\ChatController@sendProductDetails',
            'Modules\\Ticket\\Http\\Controllers\\ChatController@initiateChatWithVendor',
            'Modules\\Ticket\\Http\\Controllers\\ChatController@storeMessage',
            'Modules\\Ticket\\Http\\Controllers\\ChatController@createChat',
            'Modules\\Ticket\\Http\\Controllers\\ChatController@inboxRefresh',
        ];

        $roleIds = [2, 3];

        // Fetch all permissions in one query
        $permissions = Permission::whereIn('name', $chatPermissions)->pluck('id', 'name');

        // Prepare bulk insert
        $rowsToInsert = [];

        foreach ($permissions as $permissionName => $permissionId) {
            foreach ($roleIds as $roleId) {
                $exists = PermissionRole::where('permission_id', $permissionId)
                    ->where('role_id', $roleId)
                    ->exists();

                if (! $exists) {
                    $rowsToInsert[] = [
                        'permission_id' => $permissionId,
                        'role_id'       => $roleId,
                    ];
                }
            }
        }

        // Insert all missing rows in one go
        if (! empty($rowsToInsert)) {
            PermissionRole::insert($rowsToInsert);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@ledger')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@ledger',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'ledger',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\UnitController@index')->first()) {
            Permission::insert([
                'name' => 'App\\Http\\Controllers\\UnitController@index',
                'controller_path' => 'App\\Http\\Controllers\\UnitController',
                'controller_name' => 'UnitController',
                'method_name' => 'index',
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\UnitController@store')->first()) {
            Permission::insert([
                'name' => 'App\\Http\\Controllers\\UnitController@store',
                'controller_path' => 'App\\Http\\Controllers\\UnitController',
                'controller_name' => 'UnitController',
                'method_name' => 'store',
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\UnitController@update')->first()) {
            Permission::insert([
                'name' => 'App\\Http\\Controllers\\UnitController@update',
                'controller_path' => 'App\\Http\\Controllers\\UnitController',
                'controller_name' => 'UnitController',
                'method_name' => 'update',
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\UnitController@destroy')->first()) {
            Permission::insert([
                'name' => 'App\\Http\\Controllers\\UnitController@destroy',
                'controller_path' => 'App\\Http\\Controllers\\UnitController',
                'controller_name' => 'UnitController',
                'method_name' => 'destroy',
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\VendorController@sendChangeOtp')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\VendorController@sendChangeOtp',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\VendorController',
                'controller_name' => 'VendorController',
                'method_name' => 'sendChangeOtp',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\VendorController@verifyChangeOtp')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\VendorController@verifyChangeOtp',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\VendorController',
                'controller_name' => 'VendorController',
                'method_name' => 'verifyChangeOtp',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\VendorController@resendChangeOtp')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\VendorController@resendChangeOtp',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\VendorController',
                'controller_name' => 'VendorController',
                'method_name' => 'resendChangeOtp',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
    }
}
