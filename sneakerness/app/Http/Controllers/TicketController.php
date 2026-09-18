<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $dbError = false;
        $errorMessage = null;

        // KPI Statistieken zoals weergegeven in het ontwerp
        $totalTicketsCount = '3.420';
        $validTicketsCount = '3.180';
        $attentionTicketsCount = '48';
        $scannedTicketsCount = '1.890';
        $capacityPercentage = '76%';

        try {
            // =========================================================================
            // DEMO VOOR DOCENT (Unhappy Scenario):
            // Verander false naar true om de database foutmelding live te demonstreren!
            // =========================================================================
            $simulateDbError = false;

            if ($simulateDbError || $request->has('db_error') || $request->has('simulate_db_error')) {
                throw new \Exception('Database is momenteel niet beschikbaar');
            }

            // Controleer actieve database verbinding
            DB::connection()->getPdo();

            $query = Ticket::query()->with(['bezoeker', 'evenement', 'prijs']);

            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

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

            // Paginatie met 6 tickets per pagina, geordend op TicketCode
            $tickets = $query->orderBy('TicketCode', 'asc')->paginate(6)->withQueryString();

        } catch (Throwable $e) {
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
}
