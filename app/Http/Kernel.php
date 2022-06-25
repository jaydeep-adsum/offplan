<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'checkrole' => \App\Http\Middleware\CheckRole::class,

        'add_features' => \App\Http\Middleware\Features\Add_features::class,
        'delete_features' => \App\Http\Middleware\Features\Delete_features::class,
        'read_features' => \App\Http\Middleware\Features\Read_features::class,

        'add_milestone' => \App\Http\Middleware\Milestone\Add_milestone::class,
        'delete_milestone' => \App\Http\Middleware\Milestone\Delete_milestone::class,
        'read_milestone' => \App\Http\Middleware\Milestone\Read_milestone::class,

        'add_developer' => \App\Http\Middleware\Developer\Add_developer::class,
        'delete_developer' => \App\Http\Middleware\Developer\Delete_developer::class,
        'edit_developer' => \App\Http\Middleware\Developer\Edit_developer::class,
        'read_developer' => \App\Http\Middleware\Developer\Read_developer::class,

        'add_community' => \App\Http\Middleware\Community\Add_community::class,
        'delete_community' => \App\Http\Middleware\Community\Delete_community::class,
        'edit_community' => \App\Http\Middleware\Community\Edit_community::class,
        'read_community' => \App\Http\Middleware\Community\Read_community::class,

        'add_unit' => \App\Http\Middleware\Unit\Add_unit::class,
        'delete_unit' => \App\Http\Middleware\Unit\Delete_unit::class,
        'edit_unit' => \App\Http\Middleware\Unit\Edit_unit::class,
        'read_unit' => \App\Http\Middleware\Unit\Read_unit::class,
    ];
}
