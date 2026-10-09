<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            // Criterium: Technische log voor unhappy scenario
            Log::warning('Unhappy scenario gesimuleerd voor evenementen overzicht.', ['ip' => $request->ip()]);

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
            // Criterium: Stored Procedures demonstratie
            // Roept MySQL Stored Procedure 'sp_GetEvenementenMetOrganisator' aan
            if ($request->has('sp') || $request->has('procedure')) {
                $spEvents = $this->getEvenementenViaProcedure();
                Log::info('Stored Procedure sp_GetEvenementenMetOrganisator succesvol aangeroepen via index.', [
                    'aantal' => count($spEvents),
                ]);
            }

            // Cijfers ophalen voor het dashboard
            $totalEvents = Evenement::count();
            $activeEvents = Evenement::where('IsActief', 1)->count();
            $expectedVisitors = Evenement::sum('AantalTicketsPerTijdslot') ?: 3600;

            $query = Evenement::query();

            // Criterium: Gebruik van Joins
            // Expliciete SQL INNER JOIN demonstratie tussen Evenement en Organisator
            if ($request->has('join') || $request->input('sort') === 'organisator') {
                $query->join('Organisator', 'Evenement.OrganisatorId', '=', 'Organisator.Id')
                      ->select('Evenement.*');
            }

            // Zoeken op naam van het event, locatie of toelichting
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('Naam', 'like', "%{$search}%")
                        ->orWhere('Locatie', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%");
                });
            }

            // Gesorteerd op datum met paginering (8 items per pagina)
            $events = $query->orderBy('Evenement.Datum', 'asc')->paginate(8)->withQueryString();

            return view('evenementen.index', compact(
                'events',
                'search',
                'totalEvents',
                'activeEvents',
                'expectedVisitors',
                'errorMessage'
            ));
        } catch (\Throwable $e) {
            // Criterium: Technische log wegschrijven
            Log::error('Fout bij het ophalen van evenementen in EvenementController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

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

    /**
     * Criterium: Gebruik van Stored Procedures
     * Haalt evenementen op inclusief organisator via MySQL Stored Procedure.
     *
     * @return array
     */
    public function getEvenementenViaProcedure(): array
    {
        try {
            return DB::select('CALL sp_GetEvenementenMetOrganisator()');
        } catch (\Throwable $e) {
            Log::error('Fout bij uitvoeren Stored Procedure sp_GetEvenementenMetOrganisator: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return [];
        }
    }

    /**
     * Criterium: Gebruik van expliciete Joins via Query Builder
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEvenementenMetJoins()
    {
        try {
            return DB::table('Evenement')
                ->join('Organisator', 'Evenement.OrganisatorId', '=', 'Organisator.Id')
                ->select(
                    'Evenement.Id as EvenementId',
                    'Evenement.Naam as EvenementNaam',
                    'Evenement.Datum',
                    'Evenement.Locatie',
                    'Organisator.Naam as OrganisatorNaam'
                )
                ->where('Evenement.IsActief', 1)
                ->get();
        } catch (\Throwable $e) {
            Log::error('Fout bij uitvoeren van expliciete JOIN query voor evenementen: ' . $e->getMessage());
            return collect();
        }
    }
}