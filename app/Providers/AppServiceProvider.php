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
use Illuminate\Support\ServiceProvider;

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
    }
}
