<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Logout Inactive user.
     *
     * @return void
     */
    public function __construct(Factory $auth)
    {
        parent::__construct($auth);

        $user = auth()->user();
        if (empty($user)) {
            return;
        }

        if ($user->status != 'Active') {
            if (Auth::guard('api')->check()) {
                Auth::guard('api')->user()->token()->delete();
            } else {
                return \Redirect::to(route('users.logout'))->send();
            }
        }

        if (Request()->is('vendor/*')) {

            if (
                ($user->roles->first()->type == 'vendor' && empty($vendor = $user->vendors()->first())) ||
                (! empty($vendor = $user->vendors()->first()) && $vendor->status != 'Active')
            ) {

                if (str_contains(url()->previous(), '/admin') && session('impersonator')) {

                    return redirect()->route('impersonator-cancel');
                }

                if (isActive('SaaS')) {
                    return \Redirect::to(route('users.logout'))->send();
                }

                \Session::flash('fail', __('Your seller account is not activate yet. Please contact with site administrator.'));

                return \Redirect::to(route('site.index'))->send();
            }
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
