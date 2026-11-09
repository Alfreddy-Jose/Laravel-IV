<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Habilitar Sanctum para SPA (Crucial para las cookies HttpOnly)
        $middleware->statefulApi();

        // 2. Tus alias de middlewares personalizados
        $middleware->alias([
            'lapso.context' => \App\Http\Middleware\SetLapsoContext::class,
        ]);

        // 3. Configuración de proxies
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
