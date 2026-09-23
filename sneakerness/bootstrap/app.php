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
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6);
            $search = trim($request->input('q', ''));
            $selectedCategory = trim($request->input('category', ''));

            if ($request->is('events*')) {
                return response()->view('evenementen.index', [
                    'events' => $emptyPaginator,
                    'search' => $search,
                    'totalEvents' => 0,
                    'activeEvents' => 0,
                    'dbError' => true,
                    'errorMessage' => 'Database is momenteel niet beschikbaar, de events konden niet worden geladen. Probeer het later opnieuw.',
                ], 200);
            }

            if ($request->is('tickets*')) {
                return response()->view('tickets.index', [
                    'tickets' => $emptyPaginator,
                    'search' => $search,
                    'totalTicketsCount' => '0',
                    'validTicketsCount' => '0',
                    'attentionTicketsCount' => '0',
                    'scannedTicketsCount' => '0',
                    'capacityPercentage' => '0%',
                    'dbError' => true,
                    'errorMessage' => 'Database is momenteel niet beschikbaar, de tickets konden niet worden geladen. Probeer het later opnieuw.',
                ], 200);
            }

            if ($request->is('contactpersonen*')) {
                return response()->view('contactpersonen.index', [
                    'contactpersonen' => $emptyPaginator,
                    'search' => $search,
                    'totalContactpersonen' => 0,
                    'linkedCount' => 0,
                    'unlinkedCount' => 0,
                    'primaryCount' => 0,
                    'dbError' => true,
                    'errorMessage' => 'Database is momenteel niet beschikbaar, de contactpersonen konden niet worden geladen. Probeer het later opnieuw.',
                ], 200);
            }

            if ($request->is('stands*')) {
                return response()->view('stands.index', [
                    'stands' => $emptyPaginator,
                    'search' => $search,
                    'selectedCategory' => $selectedCategory,
                    'totalStands' => 0,
                    'countAAPlus' => 0,
                    'countAA' => 0,
                    'countA' => 0,
                    'rentedStandsCount' => 0,
                    'availableStandsCount' => 0,
                    'dbError' => true,
                    'errorMessage' => 'Database is momenteel niet beschikbaar, de stands konden niet worden geladen. Probeer het later opnieuw.',
                ], 200);
            }

            if ($request->is('verkopers*')) {
                return response()->view('verkopers.index', [
                    'verkopers' => $emptyPaginator,
                    'search' => $search,
                    'selectedCategory' => $selectedCategory,
                    'totalVerkopers' => 0,
                    'countAAPlus' => 0,
                    'countAA' => 0,
                    'countA' => 0,
                    'partnerCount' => 0,
                    'rentedStandsCount' => 0,
                    'totalStandsCount' => 0,
                    'dbError' => true,
                    'errorMessage' => 'Database is momenteel niet beschikbaar, de verkopers konden niet worden geladen. Probeer het later opnieuw.',
                ], 200);
            }

            return response()->view('home.index', [
                'verkopers' => collect(),
                'totalVerkopers' => 0,
                'partnerCount' => 0,
                'rentedStandsCount' => 0,
                'totalStands' => 55,
                'categories' => collect(),
                'dbError' => true,
                'errorMessage' => 'Database is momenteel niet beschikbaar. Probeer het later opnieuw.',
            ], 200);
        });
    })->create();
