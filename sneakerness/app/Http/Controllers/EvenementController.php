<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class EvenementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $errorMessage = null;

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
            $totalEvents = Evenement::count();
            $activeEvents = Evenement::where('IsActief', 1)->count();
            $expectedVisitors = Evenement::sum('AantalTicketsPerTijdslot') ?: 3600;

            $query = Evenement::query();

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('Naam', 'like', "%{$search}%")
                        ->orWhere('Locatie', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%");
                });
            }

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