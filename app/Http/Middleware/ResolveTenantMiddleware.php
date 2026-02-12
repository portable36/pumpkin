<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ResolveTenantMiddleware
{
    /**
     * Resolve vendor from subdomain or domain
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        // Check if it's a vendor subdomain
        if (str_contains($host, '.')) {
            $parts = explode('.', $host);
            if (count($parts) > 2) {
                $subdomain = $parts[0];
                if ($subdomain !== 'www' && $subdomain !== 'api') {
                    $vendor = \App\Models\Vendor::where('slug', $subdomain)
                        ->where('is_active', true)
                        ->where('is_verified', true)
                        ->first();

                    if ($vendor) {
                        $request->attributes->set('vendor', $vendor);
                        config(['app.vendor_id' => $vendor->id]);
                    }
                }
            }
        }

        return $next($request);
    }
}
