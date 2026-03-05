<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 *
 * @created 24-03-2021
 * modified 25-01-2022
 */

namespace App\Http\Middleware\Api;

use App\Http\Middleware\Concerns\HandlesPermissionPath;
use Closure;

/**
 * Check user has the permission to see the records or not
 */
class CheckPermission
{
    use HandlesPermissionPath;

    /**
     *  Handle an incoming request.
     *
     * @param  Request  $request
     * @param  string  $permissions
     * @return json $data
     */
    public function handle($request, Closure $next)
    {
        if (! hasPermission()) {
            $permissionPath = $this->getPermissionPath($request);
            $message = __('You do not have permission to access these records.');

            if ($permissionPath) {
                $message = __('You do not have permission to access these records. Required permission: :x', ['x' => $permissionPath]);
            }

            $data['status']  = ['code' => 403, 'text' => __('Forbidden')];
            $data['message'] = $message;

            return response()->json(['response' => $data]);
        }

        return $next($request);
    }
}
