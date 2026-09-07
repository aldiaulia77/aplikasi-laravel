<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/health', // health check endpoint, tidak membocorkan info sensitif
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'active' => EnsureAccountIsActive::class,
        ]);

        // Rate limiter untuk login didefinisikan di RouteServiceProvider
        // bawaan Laravel 12 lewat throttle:login (lihat routes/web.php).
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
