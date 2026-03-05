<?php

namespace Database\Seeders\versions\v4_2_0;

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
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@edit')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@edit',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'edit',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@destroy')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@destroy',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'destroy',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@create')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@create',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'create',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@edit')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@edit',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'edit',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@destroy')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@destroy',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'destroy',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@index',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'index',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@create')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@create',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'create',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@store')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@store',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'store',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@edit')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@edit',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'edit',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@update')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@update',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'update',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@destroy')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController@destroy',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerAddressController',
                'controller_name' => 'CustomerAddressController',
                'method_name' => 'destroy',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@findUser')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@findUser',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'findUser',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@payment')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@payment',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'payment',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@paymentStore')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@paymentStore',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'paymentStore',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@view')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@view',
                'controller_path' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController',
                'controller_name' => 'PurchaseController',
                'method_name' => 'view',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@print')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@print',
                'controller_path' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController',
                'controller_name' => 'PurchaseController',
                'method_name' => 'print',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@payment')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@payment',
                'controller_path' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController',
                'controller_name' => 'PurchaseController',
                'method_name' => 'payment',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@ledger')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@ledger',
                'controller_path' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController',
                'controller_name' => 'SupplierController',
                'method_name' => 'ledger',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@payment')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@payment',
                'controller_path' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController',
                'controller_name' => 'SupplierController',
                'method_name' => 'payment',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@paymentStore')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@paymentStore',
                'controller_path' => 'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController',
                'controller_name' => 'SupplierController',
                'method_name' => 'paymentStore',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        // TerminalController resetPin
        if (! Permission::where('name', 'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@resetPin')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@resetPin',
                'controller_path' => 'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController',
                'controller_name' => 'TerminalController',
                'method_name' => 'resetPin',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
    }
}
