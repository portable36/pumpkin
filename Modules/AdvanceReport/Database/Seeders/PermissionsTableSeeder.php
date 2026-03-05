<?php

namespace Modules\AdvanceReport\Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionRole;
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
        if (! Permission::where('name', 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@index')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@index',
                'controller_path' => 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController',
                'controller_name' => 'AdvanceReportController',
                'method_name' => 'index',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@show')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@show',
                'controller_path' => 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController',
                'controller_name' => 'AdvanceReportController',
                'method_name' => 'show',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        if (! Permission::where('name', 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@export')->first()) {
            $permissionId = Permission::insertGetId([
                'name' => 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@export',
                'controller_path' => 'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController',
                'controller_name' => 'AdvanceReportController',
                'method_name' => 'export',
            ]);

            PermissionRole::insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }
    }
}
