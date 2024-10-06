<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

header('Access-Control-Allow-Origin: http://localhost:3001');
//header('Access-Control-Allow-Origin: http://localhost:43650');
//header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(
            except: ['*']
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
