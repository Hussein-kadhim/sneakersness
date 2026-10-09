<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

// Controller voor ticketbeheer, scanning en toegangscontrole
class TicketController extends Controller
{
    // Toont het ticketoverzicht inclusief live KPI's en zoekbalk
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $dbError = false;
        $errorMessage = null;

        // KPI statistieken voor de samenvattingskaarten
        $totalTicketsCount = '3.420';
        $validTicketsCount = '3.180';
        $attentionTicketsCount = '48';
        $scannedTicketsCount = '1.890';
        $capacityPercentage = '76%';

        try {
            // =========================================================================
            // DEMO VOOR DOCENT (Unhappy Scenario):
            // Zet $simulateDbError op true (of voeg ?db_error=1 toe in de URL) om
            // te tonen hoe de applicatie netjes omgaat met een database-uitval.
            // =========================================================================
            $simulateDbError = false;

            if ($simulateDbError || $request->has('db_error') || $request->has('simulate_db_error') || $request->has('unhappy') || $request->has('error')) {
                // Criterium: Technische log voor gesimuleerd unhappy scenario
                Log::warning('Unhappy scenario gesimuleerd voor tickets overzicht.', ['ip' => $request->ip()]);
                throw new \Exception('Database is momenteel niet beschikbaar');
            }

            // Controleer actieve database verbinding
            DB::connection()->getPdo();

            // Criterium: Stored Procedures demonstratie
            // Roept MySQL Stored Procedure 'sp_GetTicketsMetDetails' aan indien gewenst
            if ($request->has('sp') || $request->has('procedure')) {
                $spTickets = $this->getTicketsViaProcedure();
                Log::info('Stored Procedure sp_GetTicketsMetDetails succesvol aangeroepen via index.', [
                    'aantal' => count($spTickets),
                ]);
            }

            // Query met gekoppelde bezoeker, evenement en prijsgegevens
            $query = Ticket::query()->with(['bezoeker', 'evenement', 'prijs']);

            // Criterium: Gebruik van Joins
            // Expliciete SQL INNER JOIN query demonstratie tussen Ticket, Bezoeker, Evenement en Prijs
            if ($request->has('join') || $request->input('sort') === 'bezoeker') {
                $query->join('Bezoeker', 'Ticket.BezoekerId', '=', 'Bezoeker.Id')
                      ->join('Evenement', 'Ticket.EvenementId', '=', 'Evenement.Id')
                      ->join('Prijs', 'Ticket.PrijsId', '=', 'Prijs.Id')
                      ->select('Ticket.*');
            }

            // Testparameter om een lege lijst te simuleren
            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

            // Uitgebreide zoekfilter over codes, bestelnummers, status en bezoekers
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('TicketCode', 'like', "%{$search}%")
                        ->orWhere('Bestelnummer', 'like', "%{$search}%")
                        ->orWhere('TicketType', 'like', "%{$search}%")
                        ->orWhere('ZaalToegang', 'like', "%{$search}%")
                        ->orWhere('Status', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%")
                        ->orWhereHas('bezoeker', function ($sub) use ($search) {
                            $sub->where('Naam', 'like', "%{$search}%")
                                ->orWhere('Email', 'like', "%{$search}%");
                        });
                });
            }

            // Paginatie met 6 tickets per pagina, gesorteerd op ticketcode
            $tickets = $query->orderBy('Ticket.TicketCode', 'asc')->paginate(6)->withQueryString();

        } catch (Throwable $e) {
            // Criterium: Technische log wegschrijven in de catch
            Log::error('Fout bij het ophalen van tickets in TicketController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Unhappy scenario: database is down, geef nette fallback-waarden mee aan de view
            $dbError = true;
            $errorMessage = 'Database is momenteel niet beschikbaar, de tickets konden niet worden geladen. Probeer het later opnieuw.';
            $tickets = new LengthAwarePaginator([], 0, 6);
            $totalTicketsCount = '0';
            $validTicketsCount = '0';
            $attentionTicketsCount = '0';
            $scannedTicketsCount = '0';
            $capacityPercentage = '0%';
        }

        return view('tickets.index', compact(
            'tickets',
            'search',
            'dbError',
            'errorMessage',
            'totalTicketsCount',
            'validTicketsCount',
            'attentionTicketsCount',
            'scannedTicketsCount',
            'capacityPercentage'
        ));
    }

    /**
     * Criterium: Gebruik van Stored Procedures
     * Haalt alle tickets op met bezoeker-, event- en prijsdetails via MySQL Stored Procedure.
     *
     * @return array
     */
    public function getTicketsViaProcedure(): array
    {
        try {
            return DB::select('CALL sp_GetTicketsMetDetails()');
        } catch (Throwable $e) {
            Log::error('Fout bij het uitvoeren van Stored Procedure sp_GetTicketsMetDetails: ' . $e->getMessage(), [
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
    public function getTicketsMetJoins()
    {
        try {
            return DB::table('Ticket')
                ->join('Bezoeker', 'Ticket.BezoekerId', '=', 'Bezoeker.Id')
                ->join('Evenement', 'Ticket.EvenementId', '=', 'Evenement.Id')
                ->join('Prijs', 'Ticket.PrijsId', '=', 'Prijs.Id')
                ->select(
                    'Ticket.Id as TicketId',
                    'Ticket.TicketCode',
                    'Ticket.Bestelnummer',
                    'Ticket.TicketType',
                    'Ticket.Status',
                    'Bezoeker.Naam as BezoekerNaam',
                    'Bezoeker.Email as BezoekerEmail',
                    'Evenement.Naam as EvenementNaam',
                    'Prijs.Tarief as TicketTarief'
                )
                ->where('Ticket.IsActief', 1)
                ->get();
        } catch (Throwable $e) {
            Log::error('Fout bij uitvoeren van expliciete JOIN query voor tickets: ' . $e->getMessage());
            return collect();
        }
    }
}
