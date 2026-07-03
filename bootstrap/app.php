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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'events.active' => \App\Http\Middleware\EnsureEventsActive::class,
        ]);

        // Paystack and Twilio post webhook events without a Laravel CSRF token; authenticity is
        // instead verified via signature headers in the respective controllers.
        $middleware->validateCsrfTokens(except: [
            'webhooks/paystack',
            'webhooks/twilio/status',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
