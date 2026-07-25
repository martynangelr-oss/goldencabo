<?php

use App\Http\Middleware\CheckCmsPermission;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SessionTimeout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'cms.permission' => CheckCmsPermission::class,
            'session.timeout' => SessionTimeout::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        $middleware->trustProxies(at: '*');

        $middleware->throttleApi();

        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (PostTooLargeException $e, $request) {
            $msg = 'El archivo es demasiado grande. Máximo permitido: 10 MB por imagen.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 413);
            }

            return back()->withErrors(['image' => $msg]);
        });
    })->create();
