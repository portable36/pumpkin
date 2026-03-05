<?php

namespace Modules\Delivery\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLicenseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $user = auth()->user();
        if (($user->roles->first()->slug == 'delivery-man') && (! empty($deliveryMan = $user->deliveryMan()->first()) && $deliveryMan->license_status == 'verified')) {
            return $next($request);
        }

        if (Auth::guard('api')->check()) {
            Auth::guard('api')->user()->token()->delete();

            throw new AuthenticationException();
        }

        return redirect()->route('site.index');
    }
}
