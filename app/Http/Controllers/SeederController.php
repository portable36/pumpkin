<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Unit;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Modules\MenuBuilder\Http\Models\MenuItems;

class SeederController extends Controller
{
    /**
     * Load seed data
     * date: 10-09-2025
     */
    public function loadSeedData(Request $request)
    {
        if ($request->has('identity') && $request->identity == 'techvillage-shop') {
            try {
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

                MenuItems::addRouteOnParams('pos/setup', '3', 'vendor.pos.customer');
                MenuItems::addRouteOnParams('pos/setup', '6', 'vendor.pos.customer');

                Artisan::call('optimize:clear');
            } catch (\Exception $e) {
                return redirect()->back()->withFail($e->getMessage());
            }

            return redirect()->back()->withSuccess(__('Seed data successfully loaded.'));
        }

        return redirect()->back()->withFail(__('You are not allowed to load seed data.'));
    }

    /**
     * Load seed data
     * date: 10-09-2025
     */
    public function loadLiveSeedData(Request $request)
    {
        if ($request->has('identity') && $request->identity == 'techvillage-shop') {
            try {

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

                    MenuItems::addRouteOnParams('pos/setup', '3', 'vendor.pos.customer');
                    MenuItems::addRouteOnParams('pos/setup', '6', 'vendor.pos.customer');
                    
                }

                Artisan::call('optimize:clear');
            } catch (\Exception $e) {
                return redirect()->back()->withFail($e->getMessage());
            }

            return redirect()->back()->withSuccess(__('Seed data successfully loaded.'));
        }

        return redirect()->back()->withFail(__('You are not allowed to load seed data.'));
    }
}
