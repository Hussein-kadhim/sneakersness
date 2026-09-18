<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/login',
            users: '/verkopers',
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Database\QueryException|\PDOException $e, Request $request) {
            if ($request->is('tickets*')) {
                return response()->view('tickets.index', [
                    'tickets' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6),
                    'search' => '',
                    'dbError' => true,
                    'errorMessage' => 'Database is momenteel niet beschikbaar, de tickets konden niet worden geladen. Probeer het later opnieuw.',
                    'totalTicketsCount' => '0',
                    'validTicketsCount' => '0',
                    'attentionTicketsCount' => '0',
                    'scannedTicketsCount' => '0',
                    'capacityPercentage' => '0%',
                ], 200);
            }
        });
    })->create();
