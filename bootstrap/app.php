<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'support' => \App\Http\Middleware\SupportMiddleware::class,
            'admin_or_support' => \App\Http\Middleware\AdminOrSupportMiddleware::class,
            'locale' => \App\Http\Middleware\LocaleMiddleware::class,
            'inactive' => \App\Http\Middleware\InactivityMiddleware::class,
            'lowercase' => \App\Http\Middleware\LowercaseInputMiddleware::class,
            'verified_code' => \App\Http\Middleware\EnsureEmailCodeVerified::class,
        ]);
        // Añadir middlewares al grupo 'web'
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\LowercaseInputMiddleware::class,
            \App\Http\Middleware\LocaleMiddleware::class,
            \App\Http\Middleware\InactivityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
