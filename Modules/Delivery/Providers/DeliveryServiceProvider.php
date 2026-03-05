<?php

namespace Modules\Delivery\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Delivery\Http\Middleware\CheckLicenseMiddleware;

class DeliveryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {}

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router']->aliasMiddleware('checkedDeliveryLicense', CheckLicenseMiddleware::class);
    }
}
