<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeQueries
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enable query caching
        if (config('app.env') === 'production') {
            \Illuminate\Database\Eloquent\Builder::macro(
                'remember',
                function ($minutes = 60) {
                    return $this->cache($minutes);
                }
            );
        }

        return $next($request);
    }
}
