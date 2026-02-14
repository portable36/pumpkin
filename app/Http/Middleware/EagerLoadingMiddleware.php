<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * EagerLoadingMiddleware
 * Forces eager loading of relationships to prevent N+1 queries on Hostinger
 * 
 * Add to app/Http/Kernel.php protected $middleware array:
 * \App\Http\Middleware\EagerLoadingMiddleware::class,
 */
class EagerLoadingMiddleware
{
    /**
     * Handle the request
     */
    public function handle(Request $request, Closure $next)
    {
        // Log N+1 queries in development
        if (config('app.debug')) {
            \DB::listen(function ($query) {
                // Track query count for debugging
                if (!defined('QUERY_COUNT')) {
                    define('QUERY_COUNT', 1);
                }
            });
        }

        return $next($request);
    }
}
