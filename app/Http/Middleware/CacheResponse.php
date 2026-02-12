<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cache GET requests only
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Cache product pages for 24 hours
            if ($request->is('products/*') || $request->is('categories/*')) {
                $response->header('Cache-Control', 'public, max-age=86400');
                $response->header('ETag', md5($response->getContent()));
            }
            // Cache API responses for 1 hour
            elseif ($request->is('api/*')) {
                $response->header('Cache-Control', 'public, max-age=3600');
            }
            // Cache static assets for 30 days
            elseif ($request->is('*/css/*', '*/js/*', '*/images/*')) {
                $response->header('Cache-Control', 'public, max-age=2592000, immutable');
            }
        }

        // Set security headers
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
