<?php

namespace Database\Seeders\versions\v3_1_1;

use App\Models\Permission;
use App\Models\PermissionRole;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@create')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@index',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'index',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@create')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@create',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'create',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@store')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@store',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'store',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@edit')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@edit',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'edit',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@update')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@update',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'update',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@destroy')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@destroy',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'destroy',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@suggestion')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@suggestion',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'suggestion',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\BrandController@assigBrand')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\BrandController@assigBrand',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\BrandController',
                'controller_name' => 'BrandController',
                'method_name' => 'assigBrand',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }



        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\AttributeController@create')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\AttributeController@index',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\AttributeController',
                'controller_name' => 'AttributeController',
                'method_name' => 'index',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\AttributeController@create')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\AttributeController@create',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\AttributeController',
                'controller_name' => 'AttributeController',
                'method_name' => 'create',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\AttributeController@store')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\AttributeController@store',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\AttributeController',
                'controller_name' => 'AttributeController',
                'method_name' => 'store',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\AttributeController@edit')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\AttributeController@edit',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\AttributeController',
                'controller_name' => 'AttributeController',
                'method_name' => 'edit',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\AttributeController@update')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\AttributeController@update',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\AttributeController',
                'controller_name' => 'AttributeController',
                'method_name' => 'update',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
        if (! Permission::where('name', 'App\\Http\\Controllers\\Vendor\\AttributeController@destroy')->first()) {

            $permissionId = Permission::insertGetId([
                'name' => 'App\\Http\\Controllers\\Vendor\\AttributeController@destroy',
                'controller_path' => 'App\\Http\\Controllers\\Vendor\\AttributeController',
                'controller_name' => 'AttributeController',
                'method_name' => 'destroy',
            ]);
            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
    }
}
