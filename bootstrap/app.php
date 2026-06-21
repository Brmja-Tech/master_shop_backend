<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/dashboard.php'));

            Route::middleware('api')
                ->group(base_path('routes/delivery.php'));
        }
    )
 ->withMiddleware(function (Middleware $middleware) {

    $middleware->appendToGroup('api', SetLocale::class);

    $middleware->redirectGuestsTo(function () {
        if (request()->is('*/dashboard/*')) {
            return route('dashboard.login');
        } else {
            return route('login');
        }
    });

    $middleware->redirectUsersTo(function () {
        if (Auth::guard('admin')->check()) {
            return route('dashboard.home');
        } else {
            return route('home');
        }
    });

    $middleware->alias([

        /**** OTHER MIDDLEWARE ALIASES ****/

        'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
        'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
        'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        'setLocale' => SetLocale::class,

        // Vendor
        'vendor.auth' => \App\Http\Middleware\VendorAuthMiddleware::class,
        'delivery.auth' => \App\Http\Middleware\DeliveryAuthMiddleware::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'user.auth' => \App\Http\Middleware\UserAuthMiddleware::class,
        'user.guest' => \App\Http\Middleware\UserGuestMiddleware::class,

    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            return (new \App\Exceptions\ApiExceptionHandler)->handle($e, $request);
        });
    })->create();
