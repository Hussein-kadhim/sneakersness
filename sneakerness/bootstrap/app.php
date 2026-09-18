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
            $errorMessage = 'Er kan momenteel geen verbinding worden gemaakt met de database. Controleer of de database server is ingeschakeld.';
            $dbError = true;

            if ($request->is('contactpersonen*')) {
                $search = trim($request->input('q', ''));
                $contactpersonen = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
                $totalContactpersonen = 0;
                $linkedCount = 0;
                $unlinkedCount = 0;
                $primaryCount = 0;

                return response()->view('contactpersonen.index', compact(
                    'contactpersonen',
                    'search',
                    'totalContactpersonen',
                    'linkedCount',
                    'unlinkedCount',
                    'primaryCount',
                    'dbError',
                    'errorMessage'
                ), 500);
            }

            if ($request->is('verkopers*') || $request->is('/')) {
                $search = trim($request->input('q', ''));
                $selectedCategory = trim($request->input('category', ''));
                $verkopers = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
                $totalVerkopers = 0;
                $countAAPlus = 0;
                $countAA = 0;
                $countA = 0;
                $partnerCount = 0;
                $rentedStandsCount = 0;
                $totalStandsCount = 0;

                return response()->view('verkopers.index', compact(
                    'verkopers',
                    'search',
                    'selectedCategory',
                    'totalVerkopers',
                    'countAAPlus',
                    'countAA',
                    'countA',
                    'partnerCount',
                    'rentedStandsCount',
                    'totalStandsCount',
                    'dbError',
                    'errorMessage'
                ), 500);
            }
        });
    })->create();
