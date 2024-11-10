<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

    })
    ->withExceptions(function (Exceptions $exceptions) {

        //Custom Rendering
        $exceptions->render(function (AuthenticationException $e, Request $request) {

            return response()->json([
                'message' => 'Unauthenticated',
                'success' => false,
                'data' => 'You got shit, why are you here, who are you?'
            ], 401);
        });

    })->create();
