<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Millat <[millat.techvill@gmail.com]>
 *
 * @created 28-08-2021
 */

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\HandlesPermissionPath;
use Closure;
use Illuminate\Support\Facades\Auth;

class Permission
{
    use HandlesPermissionPath;

    public function handle($request, Closure $next)
    {
        if (session('impersonator') && $request->segment(2) != 'impersonate' && $request->segment(1) == 'admin') {
            Auth::onceUsingId(session('impersonator'));
        }
        if (! hasPermission()) {
            $permissionPath = $this->getPermissionPath($request);
            $message = __('Unauthorized! Go home, grow up and get authorization');

            if ($permissionPath) {
                $message = __('You do not have permission to access this page. Required permission: :x', ['x' => $permissionPath]);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
