<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

// Controller voor het beheren en bekijken van Sneakerness edities en evenementen
class EvenementController extends Controller
{
    // Toont de evenementenpagina met actieve edities, zoekbalk en statistieken
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $errorMessage = null;

        // Simulatie voor het unhappy scenario (database storing)
        if ($request->has('error') || $request->has('unhappy')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de events konden niet worden geladen. Probeer het later opnieuw.';
            return view('evenementen.index', [
                'events' => new LengthAwarePaginator([], 0, 8),
                'search' => $search,
                'totalEvents' => 0,
                'activeEvents' => 0,
                'expectedVisitors' => 0,
                'errorMessage' => $errorMessage,
            ]);
        }

        try {
            // Cijfers ophalen voor het dashboard
            $totalEvents = Evenement::count();
            $activeEvents = Evenement::where('IsActief', 1)->count();
            $expectedVisitors = Evenement::sum('AantalTicketsPerTijdslot') ?: 3600;

            $query = Evenement::query();

            // Zoeken op naam van het event, locatie of toelichting
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('Naam', 'like', "%{$search}%")
                        ->orWhere('Locatie', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%");
                });
            }

            // Gesorteerd op datum met paginering (8 items per pagina)
            $events = $query->orderBy('Datum', 'asc')->paginate(8)->withQueryString();

            return view('evenementen.index', compact(
                'events',
                'search',
                'totalEvents',
                'activeEvents',
                'expectedVisitors',
                'errorMessage'
            ));
        } catch (\Throwable $e) {
            // Nette foutafhandeling wanneer de database niet bereikbaar is
            $errorMessage = 'Database is momenteel niet beschikbaar, de events konden niet worden geladen. Probeer het later opnieuw.';

            return view('evenementen.index', [
                'events' => new LengthAwarePaginator([], 0, 8),
                'search' => $search,
                'totalEvents' => 0,
                'activeEvents' => 0,
                'expectedVisitors' => 0,
                'errorMessage' => $errorMessage,
            ]);
        }
    }
}