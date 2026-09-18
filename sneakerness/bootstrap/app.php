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
            $errorMessage = 'Database is momenteel niet beschikbaar, de gegevens konden niet worden geladen. Probeer het later opnieuw.';
            $dbError = true;

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
                ), 200);
            }

            if ($request->is('/')) {
                return response()->view('home.index', [
                    'verkopers' => collect(),
                    'totalVerkopers' => 0,
                    'partnerCount' => 0,
                    'rentedStandsCount' => 0,
                    'totalStands' => 55,
                    'categories' => collect(),
                    'dbError' => true,
                    'errorMessage' => 'Database is momenteel niet beschikbaar.',
                ], 200);
            }

            if ($request->is('verkopers*')) {
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
                ), 200);
            }

            if ($request->is('stands*')) {
                $search = trim($request->input('q', ''));
                $selectedCategory = trim($request->input('category', ''));
                $stands = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1, [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]);
                $totalStands = 0;
                $countAAPlus = 0;
                $countAA = 0;
                $countA = 0;
                $rentedStandsCount = 0;
                $availableStandsCount = 0;

                return response()->view('stands.index', compact(
                    'stands',
                    'search',
                    'selectedCategory',
                    'totalStands',
                    'countAAPlus',
                    'countAA',
                    'countA',
                    'rentedStandsCount',
                    'availableStandsCount',
                    'dbError',
                    'errorMessage'
                ), 200);
            }

            // Fallback voor elke andere pagina (bv. /login, /register, etc.)
            return response()->view('home.index', [
                'verkopers' => collect(),
                'totalVerkopers' => 0,
                'partnerCount' => 0,
                'rentedStandsCount' => 0,
                'totalStands' => 55,
                'categories' => collect(),
                'dbError' => true,
                'errorMessage' => $errorMessage,
            ], 200);
        });
    })->create();
