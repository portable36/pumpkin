<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\SearchService;
use App\Services\VendorService;
use App\Services\ImageService;
use App\Services\PaymentService;
use App\Services\ShippingService;
use App\Services\PayoutService;
use App\Services\VendorOnboardingService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\ReportRequested;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });

        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });

        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService($app->make(CartService::class));
        });

        $this->app->singleton(SearchService::class, function ($app) {
            return new SearchService();
        });

        $this->app->singleton(VendorService::class, function ($app) {
            return new VendorService();
        });

        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });

        $this->app->singleton(ShippingService::class, function ($app) {
            return new ShippingService();
        });

        $this->app->singleton(PayoutService::class, function ($app) {
            return new PayoutService();
        });

        $this->app->singleton(VendorOnboardingService::class, function ($app) {
            return new VendorOnboardingService();
        });

        // Bind settings helper for facade access
        $this->app->singleton('settings', function ($app) {
            return new \App\Helpers\Settings();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enforce HTTPS in production
        if (env('APP_ENV') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Set up Spatie Media Library
        \Spatie\MediaLibrary\MediaCollections\Models\Media::class;

        // API rate limiter
        RateLimiter::for('api', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Wire report event to job
        Event::listen(ReportRequested::class, function (ReportRequested $event) {
            GenerateReportJob::dispatch($event->reportId)->onQueue('reports');
        });
    }
}
