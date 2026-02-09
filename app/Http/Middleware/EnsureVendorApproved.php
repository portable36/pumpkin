<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user->vendor || ! $user->vendor->approved) {
            abort(403, 'Vendor account not approved yet.');
        }

        return $next($request);
    }
}
