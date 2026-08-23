<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserNotSuspended;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
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
        $middleware->prependToGroup('web', SecurityHeaders::class);
        $middleware->appendToGroup('web', SetLocale::class);
        $middleware->appendToGroup('web', EnsureUserNotSuspended::class);

        $middleware->appendToGroup('web', \App\Http\Middleware\VisitorTracker::class);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'admin.auth' => EnsureAdmin::class,
            'admin.guest' => RedirectIfAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            if (app()->bound('request')) {
                $request = app('request');
                try {
                    \App\Models\ErrorLog::create([
                        'user_id' => auth()->id(),
                        'url' => substr($request->fullUrl(), 0, 255),
                        'message' => substr($e->getMessage(), 0, 65500),
                        'stack_trace' => $e->getTraceAsString(),
                    ]);
                } catch (\Exception $ex) {
                    // Fail silently if DB is down
                }
            }
        });
    })->create();
