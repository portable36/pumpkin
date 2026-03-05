<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $customerRole = Role::where('slug', 'customer')->first();
        $permissionIds = PermissionRole::where('role_id', $customerRole->id)->pluck('permission_id')->toArray();

        $deliveryRole = DB::table('roles')->where('slug', 'delivery-man')->first();

        foreach ($permissionIds as $permissionId) {
            if (! DB::table('permission_roles')->where('permission_id', $permissionId)->where('role_id', $deliveryRole->id)->first()) {
                DB::table('permission_roles')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $deliveryRole->id,
                ]);
            }
        }

        $permission = Permission::where('controller_name', 'MediaManagerController')->where('method_name', 'store')->first();

        if ($permission) {
            PermissionRole::insert([
                'permission_id' => $permission->id,
                'role_id' => $deliveryRole->id,
            ]);
        }
    }
}
