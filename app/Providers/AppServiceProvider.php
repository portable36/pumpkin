<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\{
    Preference
};
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Check boot method is loaded or not.
     *
     * @var bool
     */
    public $isBooted;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {
        Schema::defaultStringLength(191);
        error_reporting(E_ALL);

        // Check if the app is installed or not & if the request is not from console
        if (config('martvill.app_install') == true) {
            View::composer('*', function ($view) {
                $data['prms'] = auth()->user()?->permissions();
                $data['view_name'] = $view->getName();
                $view->with($data);
                $this->isBooted = true;
            });
        }

        // Configure rate limiting
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('all-image', function () {
            return \Storage::disk()->allFiles('public/uploads');
        });

        $this->app->singleton('image-directories', function () {
            return \Storage::disk()->allDirectories('public');
        });

        $this->app->singleton(config('cache.prefix') . '.' . 'preferences', function ($app) {
            // Check if database is ready and app is installed
            if (! config('martvill.app_install')) {
                return collect([]);
            }

            try {
                // Ensure cache service is available
                if (! $app->bound('cache')) {
                    return collect([]);
                }

                return $app['cache']->rememberForever(config('cache.prefix') . '.' . 'preferences', function () {
                    return Preference::pluck('value', 'field');
                });
            } catch (\Exception $e) {
                // If database isn't ready or query fails, return empty collection
                return collect([]);
            }
        });
    }
}
