<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvenementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));

        try {
            $query = Evenement::query();

            if ($search !== '') {
                $query->where(function ($query) use ($search) {
                    $query->where('Naam', 'like', "%{$search}%")
                        ->orWhere('Locatie', 'like', "%{$search}%");
                });
            }

            $events = $query->orderBy('Datum')->paginate(8)->withQueryString();
            $totalEvents = Evenement::count();
            $activeEvents = Evenement::where('IsActief', true)->count();
            $upcomingEvents = Evenement::whereDate('Datum', '>=', now()->toDateString())->count();
            $expectedVisitors = Evenement::sum('AantalTicketsPerTijdslot');
            $locations = Evenement::query()->select('Locatie')->distinct()->pluck('Locatie');
        } catch (QueryException) {
            return view('evenementen.index', [
                'databaseError' => true,
                'search' => $search,
            ]);
        }

        return view('evenementen.index', compact(
            'events',
            'search',
            'totalEvents',
            'activeEvents',
            'upcomingEvents',
            'expectedVisitors',
            'locations'
        ));
    }
}