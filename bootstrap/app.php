<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserNotSuspended;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');
        $middleware->appendToGroup('web', SetLocale::class);
        $middleware->appendToGroup('web', EnsureUserNotSuspended::class);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'admin.auth' => EnsureAdmin::class,
            'admin.guest' => RedirectIfAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
