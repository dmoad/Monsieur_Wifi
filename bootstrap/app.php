<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\VerifyRadiusStatsToken;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'radius.stats' => VerifyRadiusStatsToken::class,
        ]);
        $middleware->web(append: [
            SetLocale::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('cart:send-abandonment-emails')->daily();
        $schedule->command('devices:reboot-scheduled')->everyMinute();
        $schedule->command('flows:rotate-partitions')->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
