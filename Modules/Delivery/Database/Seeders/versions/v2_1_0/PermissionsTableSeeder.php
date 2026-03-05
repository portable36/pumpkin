<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $role = Role::where('slug', 'delivery-man')->first();

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@settings')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@settings',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController',
                'controller_name' => 'DeliveryController',
                'method_name' => 'settings',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@settingStore')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@settingStore',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController',
                'controller_name' => 'DeliveryController',
                'method_name' => 'settingStore',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@index',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@edit')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@edit',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'edit',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@update')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@update',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'update',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@pdf')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@pdf',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'pdf',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@index',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController',
                'controller_name' => 'CarrierController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@create')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@create',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController',
                'controller_name' => 'CarrierController',
                'method_name' => 'create',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@store')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@store',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController',
                'controller_name' => 'CarrierController',
                'method_name' => 'store',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@show')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@show',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController',
                'controller_name' => 'CarrierController',
                'method_name' => 'show',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@edit')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@edit',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController',
                'controller_name' => 'CarrierController',
                'method_name' => 'edit',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@update')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@update',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController',
                'controller_name' => 'CarrierController',
                'method_name' => 'update',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@destroy')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@destroy',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController',
                'controller_name' => 'CarrierController',
                'method_name' => 'destroy',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@updatePassword')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@updatePassword',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController',
                'controller_name' => 'DeliveryController',
                'method_name' => 'updatePassword',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\shared\\AjaxRequestController@assignCarrier')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\shared\\AjaxRequestController@assignCarrier',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\shared\\AjaxRequestController',
                'controller_name' => 'AjaxRequestController',
                'method_name' => 'assignCarrier',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\shared\\AjaxRequestController@searchAvailableCarrier')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\shared\\AjaxRequestController@searchAvailableCarrier',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\shared\\AjaxRequestController',
                'controller_name' => 'AjaxRequestController',
                'method_name' => 'searchAvailableCarrier',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\vendor\\DeliveryOrderController@assignCarrierView')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\vendor\\DeliveryOrderController@assignCarrierView',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\vendor\\DeliveryOrderController',
                'controller_name' => 'DeliveryOrderController',
                'method_name' => 'assignCarrierView',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\DeliveryOrderController@assignCarrierView')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\DeliveryOrderController@assignCarrierView',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\DeliveryOrderController',
                'controller_name' => 'DeliveryOrderController',
                'method_name' => 'assignCarrierView',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 1,
            ]);
        }

        // / carrier permission start

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@logout')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@logout',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController',
                'controller_name' => 'ProfileController',
                'method_name' => 'logout',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController@index',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController',
                'controller_name' => 'DashboardController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController@status')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController@status',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController',
                'controller_name' => 'DashboardController',
                'method_name' => 'status',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@earning')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@earning',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController',
                'controller_name' => 'ProfileController',
                'method_name' => 'earning',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@profile')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@profile',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController',
                'controller_name' => 'ProfileController',
                'method_name' => 'profile',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@updateProfile')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@updateProfile',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController',
                'controller_name' => 'ProfileController',
                'method_name' => 'updateProfile',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@updatePassword')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@updatePassword',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController',
                'controller_name' => 'ProfileController',
                'method_name' => 'updatePassword',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@activity')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@activity',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController',
                'controller_name' => 'ProfileController',
                'method_name' => 'activity',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@index',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@setting')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@setting',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'setting',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@withdraw')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@withdraw',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'withdraw',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@assign')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@assign',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'assign',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@pickup')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@pickup',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'pickup',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@delivered')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@delivered',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'delivered',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@completed')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@completed',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'completed',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@show')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@show',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'show',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@changeStatus')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@changeStatus',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'changeStatus',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@print')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@print',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'print',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@upload')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@upload',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController',
                'controller_name' => 'MediaManagerController',
                'method_name' => 'upload',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@paginateData')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@paginateData',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController',
                'controller_name' => 'MediaManagerController',
                'method_name' => 'paginateData',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@sortFiles')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@sortFiles',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController',
                'controller_name' => 'MediaManagerController',
                'method_name' => 'sortFiles',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        // end carrier permission

        // start carrier api permission

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\DeliveryController@updateStatus')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\DeliveryController@updateStatus',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\DeliveryController',
                'controller_name' => 'DeliveryController',
                'method_name' => 'updateStatus',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController@index',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController@show')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController@show',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'show',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController@orderStatusUpdate')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController@orderStatusUpdate',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\OrderController',
                'controller_name' => 'OrderController',
                'method_name' => 'orderStatusUpdate',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@index',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@paypalSetting')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@paypalSetting',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'paypalSetting',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@bankSetting')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@bankSetting',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'bankSetting',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@withdraw')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@withdraw',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'withdraw',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@method')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@method',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'method',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@paymentMethod')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController@paymentMethod',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WithdrawalController',
                'controller_name' => 'WithdrawalController',
                'method_name' => 'paymentMethod',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WalletController@earning')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WalletController@earning',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WalletController',
                'controller_name' => 'WalletController',
                'method_name' => 'earning',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        if (! Permission::where('name', 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WalletController@wallet')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WalletController@wallet',
                'controller_path' => 'Modules\\Delivery\\Http\\Controllers\\Api\\V1\\WalletController',
                'controller_name' => 'WalletController',
                'method_name' => 'wallet',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id,
            ]);
        }

        // end carrier api permission
    }
}
