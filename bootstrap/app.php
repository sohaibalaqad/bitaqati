<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant'        => \App\Http\Middleware\ResolveTenant::class,
            'super_admin'   => \App\Http\Middleware\IsSuperAdmin::class,
            'network_admin' => \App\Http\Middleware\IsNetworkAdmin::class,
            'admin'         => \App\Http\Middleware\IsNetworkAdmin::class, // backward-compat
            'client'        => \App\Http\Middleware\IsClient::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
