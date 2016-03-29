<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */

    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Cors::class, // Place Cors middleware here
        \App\Http\Middleware\ScriptCheck::class,
        \App\Http\Middleware\AfterGetRequest::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.admin' => \App\Http\Middleware\SiteAdmin::class,
        'auth.agent' => \App\Http\Middleware\SiteAgent::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'access' => \App\Http\Middleware\AccessLevel::class,
        'admin' => \App\Http\Middleware\SiteAdmin::class,
        'agent' => \App\Http\Middleware\SiteAgent::class,
        'smember' => \App\Http\Middleware\SiteCreate::class,
    ];
}
