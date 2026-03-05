<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\{
    HttpException,
    MethodNotAllowedHttpException,
    NotFoundHttpException
};
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            // Web Routes
            Route::middleware([])->group(function () {
                require __DIR__.'/../routes/web.php';
                require __DIR__.'/../routes/site.php';
                require __DIR__.'/../routes/vendor.php';

                // Load module web routes
                if (app()->bound('modules')) {
                    foreach (app('modules')->allEnabled() as $module) {
                        $webRoutePath = module_path($module->getName(), '/Routes/web.php');
                        if (file_exists($webRoutePath)) {
                            require $webRoutePath;
                        }
                    }
                }
            });

            // API Routes
            Route::middleware('api')
                ->group(function () {
                    require __DIR__.'/../routes/api.php';
                    require __DIR__.'/../routes/vendorApi.php';
                    require __DIR__.'/../routes/userApi.php';
                });
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware
        $middleware->use([
            \App\Http\Middleware\CheckForMaintenanceMode::class,
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // Web group
        $middleware->web(append: [
            \App\Http\Middleware\EncryptCookies::class,
            \App\Http\Middleware\DatabaseUpgrade::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // API group
        $middleware->api(append: [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CheckApiAccess::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'site.auth' => \App\Http\Middleware\UserAuthenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'customer' => \App\Http\Middleware\RedirectIfNotCustomer::class,
            'permission-api' => \App\Http\Middleware\Api\CheckPermission::class,
            'isLoggedIn' => \App\Http\Middleware\IsLoggedIn::class,
            'locale' => \App\Http\Middleware\Locale::class,
            'permission' => \App\Http\Middleware\Permission::class,
            'scopes' => \Laravel\Passport\Http\Middleware\CheckScopes::class,
            'scope' => \Laravel\Passport\Http\Middleware\CheckForAnyScope::class,
            'checkForDemoMode' => \App\Http\Middleware\CheckForDemoMode::class,
            'checkGuest' => \App\Http\Middleware\CheckGuest::class,
            'affiliate' => \Modules\Affiliate\Http\Middleware\Affiliate::class,
            'themeable' => \App\Http\Middleware\Themeable::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Create a helper class to use the ApiResponse trait
        $apiResponseHelper = new class {
            use ApiResponse;

            public function handleNotFound($data = [], $message = null)
            {
                return $this->notFoundResponse($data, $message);
            }

            public function handleMethodNotAllowed()
            {
                return $this->methodNotAllowedResponse();
            }

            public function handleUnauthorized()
            {
                return $this->unauthorizedResponse();
            }

            public function handleUnprocessable($errors = [])
            {
                return $this->unprocessableResponse($errors);
            }

            public function handleServiceUnavailable()
            {
                return $this->serviceUnavailableResponse();
            }
        };

        // Don't flash these inputs on validation exceptions
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        // Handle NotFoundHttpException for API routes
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($apiResponseHelper) {
            if ($request->is('api/*')) {
                return $apiResponseHelper->handleNotFound([], __('Page Not Found.'));
            }
        });

        // Handle MethodNotAllowedHttpException for API routes
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($apiResponseHelper) {
            if ($request->is('api/*')) {
                return $apiResponseHelper->handleMethodNotAllowed();
            }
        });

        // Handle AuthenticationException for API routes
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($apiResponseHelper) {
            if ($request->is('api/*')) {
                return $apiResponseHelper->handleUnauthorized();
            }
        });

        // Handle ValidationException for API routes
        $exceptions->render(function (ValidationException $e, Request $request) use ($apiResponseHelper) {
            if ($request->is('api/*')) {
                return $apiResponseHelper->handleUnprocessable($e->errors());
            }
        });

        // Handle HttpException for API routes
        $exceptions->render(function (HttpException $e, Request $request) use ($apiResponseHelper) {
            if ($request->is('api/*')) {
                return $apiResponseHelper->handleServiceUnavailable();
            }
        });

        // Handle PostTooLargeException
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) use ($apiResponseHelper) {
            if (!$request->is('api/*')) {
                session()->flash('fail', __('Your file is too large.'));
                return redirect()->back();
            }
            return $apiResponseHelper->handleUnprocessable();
        });

        // Handle Laravel Socialite InvalidStateException
        $exceptions->render(function (\Laravel\Socialite\Two\InvalidStateException $e, Request $request) {
            return redirect()->route('site.index');
        });

        // Report exceptions (similar to reportable in old handler)
        $exceptions->report(function (Throwable $e) {
            // Custom reporting logic here if needed
        });
    })->create();
