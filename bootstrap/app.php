<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\AuthzServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->alias([
            'zitadel'  => \App\Http\Middleware\ZitadelAuth::class,
            'authz'    => \App\Http\Middleware\Authz::class,
            'internal' => \App\Http\Middleware\InternalServiceAuth::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('cart:send-abandonment-emails')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
