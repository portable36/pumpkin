<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Millat <[millat.techvill@gmail.com]>
 *
 * @created 18-09-2021
 */

namespace App\Http\Controllers;

use Cache;
use Route;
use App\Models\{
    Role,
    Permission
};

class PermissionRoleController extends Controller
{
    /**
     * generate permission from route
     *
     * @return back
     */
    public function generatePermission()
    {
        $prms = Permission::all()->toArray();
        $routeCollection = Route::getRoutes();
        $routes = [];
        $permissions = [];

        $key = 0;
        foreach ($routeCollection as $route) {

            // get all route, including api
            $action = $route->getAction();

            if (array_key_exists('controller', $action)) {

                $explodedControllerPath = explode('\\', $action['controller']);

                // If controller is Facade then skip
                if (in_array($explodedControllerPath[0], ['Facade'])) {
                    continue;
                }

                // Check if route has 'permission' or 'permission-api' middleware
                $middleware = $route->middleware();
                $hasPermissionMiddleware = in_array('permission', $middleware) ||
                                        in_array('permission-api', $middleware);

                if (! $hasPermissionMiddleware) {
                    continue;
                }

                $explodedAction = explode('@', $action['controller']);
                $explodedController = explode('\\', $explodedAction[0]);

                if ($this->isPermissionExist($action['controller'], $prms) || ! isset($explodedAction[1])) {
                    continue;
                }

                $permissions[$key]['name'] = $action['controller'];
                $permissions[$key]['controller_path'] = $explodedAction[0];
                $permissions[$key]['controller_name'] = end($explodedController);
                $permissions[$key]['method_name'] = $explodedAction[1];

                $key++;
            }
        }

        $notUsedPermission = $this->notUsedPermission($prms, $routeCollection);

        if (! empty($permissions)) {
            Permission::insertPermission($permissions);
        }

        if (! empty($notUsedPermission)) {
            Permission::whereIn('id', $notUsedPermission)->delete();
            // Clear cache after deleting permissions
            Permission::forgetCache();
        }

        // Always run the seeder regardless of inserted/deleted permissions
        \Artisan::call('db:seed', ['--class' => 'Database\Seeders\versions\v4_2_0\PermissionUpdateTableSeeder']);

        // Clear permission-role cache since permissions have changed
        Cache::forget(config('cache.prefix') . '-permission-role');

        // Clear all role-specific permission caches
        $roles = \App\Models\Role::getAll();
        foreach ($roles as $role) {
            Cache::forget(config('cache.prefix') . '-permission-name-by-role-' . $role->id);
        }

        return redirect()->back()->withSuccess(__('Permission updated successfully.'));
    }

    /**
     * check permission already exist or not
     *
     * @param  string  $permission
     * @param  array  $permissions
     * @return bool [description]
     */
    protected function isPermissionExist($permission, $permissions)
    {
        foreach ($permissions as $prm) {
            if ($this->inArrays($permission, $prm)) {
                return true;
            }
        }

        return false;
    }

    protected function inArrays($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->inArrays($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    protected function notUsedPermission($prms, $routeCollection)
    {
        $permissions = [];
        foreach ($routeCollection as $route) {
            $action = $route->getAction();
            if (array_key_exists('controller', $action)) {

                $explodedControllerPath = explode('\\', $action['controller']);
                if (in_array($explodedControllerPath[0], ['Facade'])) {
                    continue;
                }

                // Check if route has 'permission' or 'permission-api' middleware
                $middleware = $route->middleware();
                $hasPermissionMiddleware = in_array('permission', $middleware) ||
                                        in_array('permission-api', $middleware);

                // Only add to permissions if it has the required middleware
                if ($hasPermissionMiddleware) {
                    array_push($permissions, $action['controller']);
                }
            }
        }
        $notUsedPermissionIDs = [];
        foreach ($prms as $prm) {
            if (! in_array($prm['name'], $permissions)) {
                array_push($notUsedPermissionIDs, $prm['id']);
            }
        }

        return $notUsedPermissionIDs;
    }
}
