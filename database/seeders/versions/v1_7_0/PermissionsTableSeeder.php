<?php

namespace Database\Seeders\versions\v1_7_0;

use App\Models\Permission;
use App\Models\PermissionRole;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@index')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@index',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                [
                    'permission_id' => $permissionId,
                    'role_id' => 1,
                ],
                [
                    'permission_id' => $permissionId,
                    'role_id' => 2,
                ],
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@create')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@create',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'create',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@store')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@store',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'store',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

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

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\CustomerController@update')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\CustomerController@update',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\CustomerController',
                'controller_name' => 'CustomerController',
                'method_name' => 'update',
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
    }
}
