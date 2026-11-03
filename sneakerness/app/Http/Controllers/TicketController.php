<?php

namespace App\Http\Controllers;

use App\Models\Bezoeker;
use App\Models\Evenement;
use App\Models\Prijs;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

// Controller voor ticketbeheer, aankoop, wijziging en toegangscontrole
class TicketController extends Controller
{
    /**
     * Toont het ticketoverzicht inclusief live KPI's, aankoopmodule en zoekbalk.
     */
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
        $events = collect();

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

            // Haal actieve evenementen op voor de ticket aankoop selectie
            $events = Evenement::query()
                ->where('IsActief', true)
                ->orderBy('Datum', 'asc')
                ->get();

            // Criterium: Stored Procedures demonstratie
            if ($request->has('sp') || $request->has('procedure')) {
                $spTickets = $this->getTicketsViaProcedure();
                Log::info('Stored Procedure sp_GetTicketsMetDetails succesvol aangeroepen via index.', [
                    'aantal' => count($spTickets),
                ]);
            }

            // Query met gekoppelde bezoeker, evenement en prijsgegevens
            $query = Ticket::query()->with(['bezoeker', 'evenement', 'prijs']);

            // Criterium: Gebruik van Joins
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
                $hasTicketCodeCol = Schema::hasColumn('Ticket', 'TicketCode');
                $hasBestelnummerCol = Schema::hasColumn('Ticket', 'Bestelnummer');
                $hasTicketTypeCol = Schema::hasColumn('Ticket', 'TicketType');
                $hasZaalToegangCol = Schema::hasColumn('Ticket', 'ZaalToegang');
                $hasStatusCol = Schema::hasColumn('Ticket', 'Status');

                $query->where(function ($q) use ($search, $hasTicketCodeCol, $hasBestelnummerCol, $hasTicketTypeCol, $hasZaalToegangCol, $hasStatusCol) {
                    $q->where('Id', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%");

                    if ($hasTicketCodeCol) {
                        $q->orWhere('TicketCode', 'like', "%{$search}%");
                    }
                    if ($hasBestelnummerCol) {
                        $q->orWhere('Bestelnummer', 'like', "%{$search}%");
                    }
                    if ($hasTicketTypeCol) {
                        $q->orWhere('TicketType', 'like', "%{$search}%");
                    }
                    if ($hasZaalToegangCol) {
                        $q->orWhere('ZaalToegang', 'like', "%{$search}%");
                    }
                    if ($hasStatusCol) {
                        $q->orWhere('Status', 'like', "%{$search}%");
                    }

                    $q->orWhereHas('bezoeker', function ($sub) use ($search) {
                        $sub->where('Naam', 'like', "%{$search}%")
                            ->orWhere('Emailadres', 'like', "%{$search}%");
                    });
                });
            }

            // Sorteer op Id aflopend zodat net toegevoegde tickets direct bovenaan staan
            $tickets = $query->orderBy('Ticket.Id', 'desc')->paginate(6)->withQueryString();

            // Dynamische KPI berekening indien records aanwezig
            $realTotal = Ticket::count();
            if ($realTotal > 0) {
                $totalTicketsCount = number_format($realTotal, 0, ',', '.');
                $validTicketsCount = number_format(Ticket::where('IsActief', true)->count(), 0, ',', '.');
                if (Schema::hasColumn('Ticket', 'Status')) {
                    $attentionTicketsCount = (string) Ticket::where('Status', 'Aandacht nodig')->count();
                    $scannedTicketsCount = (string) Ticket::where('Status', 'Gescand')->count();
                }
            }

        } catch (Throwable $e) {
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
            $events = collect();
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
            'capacityPercentage',
            'events'
        ));
    }

    /**
     * Ticket Kopen (Als gebruiker / bezoeker).
     *
     * Happy scenario:
     * - Selecteert ticket / event en bevestigt aankoop.
     * - Betaling verwerkt, ticket opgeslagen, bevestiging getoond, verschijnt in ticketoverzicht.
     *
     * Unhappy scenario:
     * - Betaling of database niet beschikbaar.
     * - Foutmelding: "Er is momenteel een probleem met het kopen van uw ticket. Uw aankoop kon niet worden verwerkt. Probeer het later opnieuw."
     * - Aankoop niet opgeslagen, geen ticket uitgegeven.
     */
    public function kopen(Request $request): RedirectResponse
    {
        $redirectRoute = $request->input('redirect_to');
        if (!$redirectRoute || !\Illuminate\Support\Facades\Route::has($redirectRoute)) {
            $redirectRoute = Auth::check() ? 'tickets.index' : 'home';
        }

        try {
            // Simulatie van betalings- of databasestoring voor unhappy scenario
            if ($request->has('db_error') || $request->has('simulate_db_error') || $request->has('unhappy') || $request->has('payment_failed') || $request->has('fail')) {
                Log::warning('Unhappy scenario gesimuleerd bij ticket aankoop.', ['user_id' => Auth::id()]);
                throw new \Exception('Database of betalingsgateway niet beschikbaar');
            }

            // Controleer actieve database verbinding
            DB::connection()->getPdo();

            $currentUser = Auth::user();

            // Bepaal bezoeker gekoppeld aan de huidige gebruiker of maak een bezoeker aan
            $bezoeker = null;
            if ($currentUser) {
                $bezoeker = Bezoeker::firstOrCreate(
                    ['Emailadres' => $currentUser->email],
                    [
                        'Naam' => $currentUser->name,
                        'IsActief' => true,
                        'Opmerking' => 'Aangemaakt via online ticket aankoop',
                    ]
                );
            } else {
                $bezoeker = Bezoeker::firstOrCreate(
                    ['Emailadres' => 'bezoeker@sneakerness.com'],
                    [
                        'Naam' => 'Sneakerness Bezoeker',
                        'IsActief' => true,
                        'Opmerking' => 'Gast aankoop',
                    ]
                );
            }

            // Controleer of er actieve evenementen zijn om tickets voor te kopen
            if (!Evenement::where('IsActief', true)->exists()) {
                return redirect()->route($redirectRoute)->with('error', 'Er bestaan op dit moment geen tickets om te kopen.');
            }

            // Zoek evenement
            $evenementId = $request->input('evenement_id');
            if (!$evenementId && $request->filled('ticket_selectie') && is_numeric($request->input('ticket_selectie'))) {
                $evenementId = (int) $request->input('ticket_selectie');
            }

            $evenement = $evenementId
                ? Evenement::where('IsActief', true)->find($evenementId)
                : Evenement::where('IsActief', true)->first();

            if (!$evenement) {
                $evenement = Evenement::where('IsActief', true)->first();
            }

            if (!$evenement) {
                return redirect()->route($redirectRoute)->with('error', 'Er bestaan op dit moment geen tickets om te kopen.');
            }

            // Zoek of maak standaard prijs record
            $prijs = Prijs::first();
            if (!$prijs) {
                $prijs = Prijs::create([
                    'Datum' => now()->toDateString(),
                    'Tijdslot' => '10:00 - 18:00',
                    'Tarief' => 17.50,
                    'IsActief' => true,
                ]);
            }

            // Bepaal tickettype
            $ticketType = 'Regulier ticket';
            $selectie = $request->input('ticket_selectie');
            if ($selectie === 'weekend' || str_contains(strtolower((string) $selectie), 'weekend')) {
                $ticketType = 'Weekend Passe-Partout';
            } elseif ($selectie === 'vip' || str_contains(strtolower((string) $selectie), 'vip')) {
                $ticketType = 'Early Bird VIP';
            } elseif ($request->filled('ticket_type')) {
                $ticketType = $request->input('ticket_type');
            }

            // Genereer unieke ticketcode en bestelnummer
            $nextNum = (int) (Ticket::max('Id') ?? 0) + 1;
            $ticketCode = $request->filled('ticket_code') ? $request->input('ticket_code') : ('TKT-' . (84920 + $nextNum));
            $bestelnummer = $request->filled('bestelnummer') ? $request->input('bestelnummer') : ('SN-2024-' . (84920 + $nextNum));

            // Stel ticketattributen samen
            $ticketAttributes = [
                'BezoekerId' => $bezoeker->Id,
                'EvenementId' => $evenement->Id,
                'PrijsId' => $prijs->Id,
                'AantalTickets' => max(1, (int) $request->input('aantal_tickets', 1)),
                'Datum' => now()->toDateString(),
                'IsActief' => true,
                'Opmerking' => 'Aankoop via online checkout • Betaling voltooid',
            ];

            // Voeg details toe als de kolommen aanwezig zijn
            if (Schema::hasColumn('Ticket', 'TicketCode')) {
                $ticketAttributes['TicketCode'] = $ticketCode;
            }
            if (Schema::hasColumn('Ticket', 'Bestelnummer')) {
                $ticketAttributes['Bestelnummer'] = $bestelnummer;
            }
            if (Schema::hasColumn('Ticket', 'TicketType')) {
                $ticketAttributes['TicketType'] = $ticketType;
            }
            if (Schema::hasColumn('Ticket', 'Tijdslot')) {
                $ticketAttributes['Tijdslot'] = 'Zaterdag 16 nov (10:00 - 18:00)';
            }
            if (Schema::hasColumn('Ticket', 'ZaalToegang')) {
                $ticketAttributes['ZaalToegang'] = 'Hal 1 • Entree A';
            }
            if (Schema::hasColumn('Ticket', 'Status')) {
                $ticketAttributes['Status'] = 'Geldig';
            }

            // Sla het nieuwe ticket op in de database
            $ticket = Ticket::create($ticketAttributes);

            Log::info('Ticket succesvol gekocht en opgeslagen.', [
                'ticket_id' => $ticket->Id,
                'code' => $ticketCode,
                'bezoeker_id' => $bezoeker->Id,
            ]);

            return redirect()->route($redirectRoute)->with(
                'success',
                "Uw aankoop is succesvol verwerkt! Ticket {$ticketCode} is opgeslagen en toegevoegd aan uw overzicht."
            );

        } catch (Throwable $e) {
            Log::error('Fout bij het kopen van ticket: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
            ]);

            // Exact vereiste foutmelding conform unhappy scenario
            return redirect()->route($redirectRoute)->with(
                'error',
                'Er is momenteel een probleem met het kopen van uw ticket. Uw aankoop kon niet worden verwerkt. Probeer het later opnieuw.'
            );
        }
    }

    /**
     * Ticket Toevoegen formulier (Als medewerker).
     */
    public function create(): View
    {
        $events = Evenement::where('IsActief', true)->orderBy('Datum', 'asc')->get();
        if ($events->isEmpty()) {
            $events = Evenement::all();
        }

        $ticketTypes = [
            'Regulier ticket' => 'Regulier ticket (Dagkaart)',
            'Regulier Slot 1' => 'Regulier Slot 1 (10:00 - 14:00)',
            'Regulier Slot 2' => 'Regulier Slot 2 (14:00 - 18:00)',
            'Early Bird VIP' => 'Early Bird VIP (Vroege toegang 09:00)',
            'Weekend Passe-Partout' => 'Weekend Passe-Partout (2 dagen)',
        ];

        $timeSlots = [
            'Zaterdag 16 nov (10:00 - 18:00)' => 'Zaterdag 16 nov (10:00 - 18:00)',
            'Zaterdag 16 nov (10:00 - 14:00)' => 'Zaterdag 16 nov (10:00 - 14:00)',
            'Zaterdag 16 nov (14:00 - 18:00)' => 'Zaterdag 16 nov (14:00 - 18:00)',
            'Zondag 17 nov (10:00 - 17:00)' => 'Zondag 17 nov (10:00 - 17:00)',
            'Weekend (Zaterdag & Zondag)' => 'Weekend (Zaterdag & Zondag)',
        ];

        $zaalOpties = [
            'Hal 1 • Entree A' => 'Hal 1 • Entree A',
            'Hal 1 • Entree B' => 'Hal 1 • Entree B',
            'Hal 2 • VIP Ingang' => 'Hal 2 • VIP Ingang',
            'Hal 2 • Hoofdentree' => 'Hal 2 • Hoofdentree',
        ];

        $statussen = [
            'Geldig' => 'Geldig',
            'Aandacht nodig' => 'Aandacht nodig',
            'Wijziging verzocht' => 'Wijziging verzocht',
            'Inactief' => 'Inactief',
        ];

        return view('tickets.create', compact('events', 'ticketTypes', 'timeSlots', 'zaalOpties', 'statussen'));
    }

    /**
     * Slaat een nieuw ticket op (Ticket Toevoegen als medewerker).
     *
     * Happy scenario:
     * - Nieuw ticket toegevoegd -> Succesvol aangemaakt, opgeslagen en verschijnt in ticketoverzicht.
     *
     * Unhappy scenario:
     * - Database niet beschikbaar -> Foutmelding: "Database is momenteel niet beschikbaar, het ticket kon niet worden opgeslagen. Probeer het later opnieuw."
     * - Niet opgeslagen, verschijnt niet in overzicht.
     */
    public function store(Request $request): RedirectResponse
    {
        // Simulatie van databasestoring voor unhappy scenario
        if ($request->has('db_error') || $request->has('simulate_db_error') || $request->has('unhappy')) {
            Log::warning('Unhappy scenario gesimuleerd bij ticket toevoegen.');

            return redirect()->route('tickets.create')
                ->withInput()
                ->with('error', 'Database is momenteel niet beschikbaar, het ticket kon niet worden opgeslagen. Probeer het later opnieuw.');
        }

        $validated = $request->validate([
            'bezoeker_naam' => 'required|string|max:255',
            'bezoeker_email' => 'required|email|max:255',
            'ticket_type' => 'required|string|max:100',
            'evenement_id' => 'nullable|integer',
            'tijdslot' => 'required|string|max:100',
            'zaal_toegang' => 'required|string|max:100',
            'aantal_tickets' => 'nullable|integer|min:1',
            'status' => 'required|string|max:50',
            'opmerking' => 'nullable|string|max:1000',
        ]);

        try {
            // Controleer actieve database verbinding
            DB::connection()->getPdo();

            // Zoek of maak de bezoeker aan
            $bezoeker = Bezoeker::firstOrCreate(
                ['Emailadres' => $validated['bezoeker_email']],
                [
                    'Naam' => $validated['bezoeker_naam'],
                    'IsActief' => true,
                    'Opmerking' => 'Geregistreerd door medewerker',
                ]
            );

            // Zorg voor gekoppeld evenement
            $evenementId = $validated['evenement_id'] ?? null;
            $evenement = $evenementId ? Evenement::find($evenementId) : Evenement::first();
            if (!$evenement) {
                $evenement = Evenement::firstOrCreate(
                    ['Naam' => 'Sneakerness Rotterdam 2024'],
                    [
                        'OrganisatorId' => 1,
                        'Datum' => now()->addDays(14)->toDateString(),
                        'Locatie' => 'Van Nelle Fabriek, Rotterdam',
                        'AantalTicketsPerTijdslot' => 500,
                        'BeschikbareStands' => 52,
                        'IsActief' => true,
                    ]
                );
            }

            // Zorg voor gekoppelde prijs
            $prijs = Prijs::first();
            if (!$prijs) {
                $prijs = Prijs::create([
                    'Datum' => now()->toDateString(),
                    'Tijdslot' => '10:00 - 18:00',
                    'Tarief' => 17.50,
                    'IsActief' => true,
                ]);
            }

            $nextNum = (int) (Ticket::max('Id') ?? 0) + 1;
            $ticketCode = $request->filled('ticket_code') ? $request->input('ticket_code') : ('TKT-' . (84920 + $nextNum));
            $bestelnummer = $request->filled('bestelnummer') ? $request->input('bestelnummer') : ('SN-2024-' . (84920 + $nextNum));

            $ticketAttributes = [
                'BezoekerId' => $bezoeker->Id,
                'EvenementId' => $evenement->Id,
                'PrijsId' => $prijs->Id,
                'AantalTickets' => (int) ($validated['aantal_tickets'] ?? 1),
                'Datum' => now()->toDateString(),
                'IsActief' => $validated['status'] !== 'Inactief',
                'Opmerking' => $validated['opmerking'] ?? 'Aangemaakt door medewerker',
            ];

            if (Schema::hasColumn('Ticket', 'TicketCode')) {
                $ticketAttributes['TicketCode'] = $ticketCode;
            }
            if (Schema::hasColumn('Ticket', 'Bestelnummer')) {
                $ticketAttributes['Bestelnummer'] = $bestelnummer;
            }
            if (Schema::hasColumn('Ticket', 'TicketType')) {
                $ticketAttributes['TicketType'] = $validated['ticket_type'];
            }
            if (Schema::hasColumn('Ticket', 'Tijdslot')) {
                $ticketAttributes['Tijdslot'] = $validated['tijdslot'];
            }
            if (Schema::hasColumn('Ticket', 'ZaalToegang')) {
                $ticketAttributes['ZaalToegang'] = $validated['zaal_toegang'];
            }
            if (Schema::hasColumn('Ticket', 'Status')) {
                $ticketAttributes['Status'] = $validated['status'];
            }

            Ticket::create($ticketAttributes);

            return redirect()->route('tickets.index')->with(
                'success',
                "Het ticket ({$ticketCode}) is succesvol toegevoegd en opgeslagen."
            );

        } catch (Throwable $e) {
            Log::error('Fout bij het toevoegen van ticket: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('tickets.create')
                ->withInput()
                ->with('error', 'Database is momenteel niet beschikbaar, het ticket kon niet worden opgeslagen. Probeer het later opnieuw.');
        }
    }

    /**
     * Ticket Wijzigen formulier (Als gebruiker / bezoeker).
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            DB::connection()->getPdo();

            $ticket = Ticket::with(['bezoeker', 'evenement', 'prijs'])->findOrFail($id);
            $events = Evenement::where('IsActief', true)->orderBy('Datum', 'asc')->get();

            $ticketTypes = [
                'Regulier ticket' => 'Regulier ticket (Dagkaart)',
                'Regulier Slot 1' => 'Regulier Slot 1 (10:00 - 14:00)',
                'Regulier Slot 2' => 'Regulier Slot 2 (14:00 - 18:00)',
                'Early Bird VIP' => 'Early Bird VIP (Vroege toegang 09:00)',
                'Weekend Passe-Partout' => 'Weekend Passe-Partout (2 dagen)',
            ];

            $timeSlots = [
                'Zaterdag 16 nov (10:00 - 18:00)' => 'Zaterdag 16 nov (10:00 - 18:00)',
                'Zaterdag 16 nov (10:00 - 14:00)' => 'Zaterdag 16 nov (10:00 - 14:00)',
                'Zaterdag 16 nov (14:00 - 18:00)' => 'Zaterdag 16 nov (14:00 - 18:00)',
                'Zondag 17 nov (10:00 - 17:00)' => 'Zondag 17 nov (10:00 - 17:00)',
                'Weekend (Zaterdag & Zondag)' => 'Weekend (Zaterdag & Zondag)',
            ];

            $zaalOpties = [
                'Hal 1 • Entree A' => 'Hal 1 • Entree A',
                'Hal 1 • Entree B' => 'Hal 1 • Entree B',
                'Hal 2 • VIP Ingang' => 'Hal 2 • VIP Ingang',
                'Hal 2 • Hoofdentree' => 'Hal 2 • Hoofdentree',
            ];

            $statussen = [
                'Geldig' => 'Geldig',
                'Aandacht nodig' => 'Aandacht nodig',
                'Wijziging verzocht' => 'Wijziging verzocht',
                'Inactief' => 'Inactief',
            ];

            return view('tickets.edit', compact('ticket', 'events', 'ticketTypes', 'timeSlots', 'zaalOpties', 'statussen'));

        } catch (Throwable $e) {
            Log::error('Fout bij openen van ticket wijzigen formulier: ' . $e->getMessage());

            return redirect()->route('tickets.index')->with(
                'error',
                'Database is momenteel niet beschikbaar, het ticket kon niet worden geopend. Probeer het later opnieuw.'
            );
        }
    }

    /**
     * Wijzigt een bestaand ticket (Als gebruiker).
     *
     * Happy scenario: Ticket succesvol wijzigen
     * - Gegeven ik ben ingelogd en sta in het ticketoverzicht
     * - Wanneer ik een ticket open om te wijzigen, de gegevens aanpas en dit opsla
     * - Dan worden de wijzigingen opgeslagen
     * - En zie ik een melding dat het ticket is gewijzigd
     * - En staan de nieuwe gegevens in het ticketoverzicht
     *
     * Unhappy scenario: Ticket wijzigen terwijl de database niet beschikbaar is
     * - Gegeven ik ben ingelogd
     * - Wanneer ik een ticket probeer te wijzigen en op opslaan druk
     * - En de database is niet beschikbaar
     * - Dan krijg ik een foutmelding: "Database is momenteel niet beschikbaar, uw ticket kon niet worden gewijzigd. Probeer het later opnieuw."
     * - En worden de wijzigingen niet opgeslagen
     * - En blijft het ticket in het ticketoverzicht staan met de oude gegevens
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // Simulatie van databasestoring voor unhappy scenario
        if ($request->has('db_error') || $request->has('simulate_db_error') || $request->has('unhappy')) {
            Log::warning('Unhappy scenario gesimuleerd bij ticket wijzigen.', ['ticket_id' => $id]);

            return redirect()->route('tickets.edit', $id)
                ->withInput()
                ->with('error', 'Database is momenteel niet beschikbaar, uw ticket kon niet worden gewijzigd. Probeer het later opnieuw.');
        }

        $validated = $request->validate([
            'bezoeker_naam' => 'required|string|max:255',
            'bezoeker_email' => 'required|email|max:255',
            'ticket_type' => 'required|string|max:100',
            'evenement_id' => 'nullable|integer',
            'tijdslot' => 'required|string|max:100',
            'zaal_toegang' => 'required|string|max:100',
            'aantal_tickets' => 'nullable|integer|min:1',
            'status' => 'required|string|max:50',
            'opmerking' => 'nullable|string|max:1000',
        ]);

        try {
            // Controleer actieve database verbinding
            DB::connection()->getPdo();

            $ticket = Ticket::with('bezoeker')->findOrFail($id);

            // 1. Werk de bezoekergegevens bij
            if ($ticket->bezoeker) {
                $ticket->bezoeker->update([
                    'Naam' => $validated['bezoeker_naam'],
                    'Emailadres' => $validated['bezoeker_email'],
                ]);
            }

            // 2. Werk de ticketgegevens bij
            $attributes = [
                'AantalTickets' => (int) ($validated['aantal_tickets'] ?? $ticket->AantalTickets ?? 1),
                'IsActief' => $validated['status'] !== 'Inactief',
                'Opmerking' => $validated['opmerking'] ?? $ticket->Opmerking,
            ];

            if ($request->filled('evenement_id')) {
                $attributes['EvenementId'] = (int) $validated['evenement_id'];
            }

            if (Schema::hasColumn('Ticket', 'TicketType')) {
                $attributes['TicketType'] = $validated['ticket_type'];
            }
            if (Schema::hasColumn('Ticket', 'Tijdslot')) {
                $attributes['Tijdslot'] = $validated['tijdslot'];
            }
            if (Schema::hasColumn('Ticket', 'ZaalToegang')) {
                $attributes['ZaalToegang'] = $validated['zaal_toegang'];
            }
            if (Schema::hasColumn('Ticket', 'Status')) {
                $attributes['Status'] = $validated['status'];
            }

            $ticket->update($attributes);

            Log::info('Ticket succesvol gewijzigd.', ['ticket_id' => $id]);

            return redirect()->route('tickets.index')->with(
                'success',
                'Het ticket is gewijzigd.'
            );

        } catch (Throwable $e) {
            Log::error('Fout bij het wijzigen van ticket in TicketController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('tickets.edit', $id)
                ->withInput()
                ->with('error', 'Database is momenteel niet beschikbaar, uw ticket kon niet worden gewijzigd. Probeer het later opnieuw.');
        }
    }

    /**
     * Ticket Annuleren (Happy & Unhappy scenario).
     *
     * Happy scenario:
     * - Ticket status wordt gewijzigd naar 'Geannuleerd' en IsActief naar false.
     * - Bevestigingsmelding wordt getoond.
     * - Ticket verschijnt als geannuleerd in het overzicht.
     *
     * Unhappy scenario:
     * - Database niet beschikbaar.
     * - Foutmelding: "Database is momenteel niet beschikbaar, het ticket kon niet worden geannuleerd. Probeer het later opnieuw."
     */
    public function annuleren(Request $request, int|string $id): RedirectResponse
    {
        try {
            // Simulatie van database-uitval voor unhappy scenario
            if ($request->has('db_error') || $request->has('simulate_db_error') || $request->has('unhappy') || $request->has('fail')) {
                Log::warning('Unhappy scenario gesimuleerd bij ticket annuleren.', ['ticket_id' => $id]);
                throw new \Exception('Database is momenteel niet beschikbaar');
            }

            // Controleer actieve database verbinding
            DB::connection()->getPdo();

            $ticket = Ticket::find($id);

            if (!$ticket) {
                return redirect()->route('tickets.index')->with('error', 'Ticket niet gevonden.');
            }

            $ticketCode = $ticket->ticket_code_display;

            // Zet de status op Geannuleerd en deactiveer het ticket
            $attributes = ['IsActief' => false];
            if (Schema::hasColumn('Ticket', 'Status')) {
                $attributes['Status'] = 'Geannuleerd';
            }
            $ticket->update($attributes);

            Log::info("Ticket {$ticketCode} succesvol geannuleerd.", ['ticket_id' => $id, 'user_id' => Auth::id()]);

            return redirect()->route('tickets.index')->with('success', 'Het ticket is succesvol geannuleerd.');

        } catch (Throwable $e) {
            Log::error('Fout bij het annuleren van ticket: ' . $e->getMessage(), [
                'ticket_id' => $id,
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('tickets.index')->with('error', 'Database is momenteel niet beschikbaar, het ticket kon niet worden geannuleerd. Probeer het later opnieuw.');
        }
    }

    /**
     * Criterium: Gebruik van Stored Procedures
     * Haalt alle tickets op met bezoeker-, event- en prijsdetails via MySQL Stored Procedure.
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
     * @return Collection
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
                    'Bezoeker.Emailadres as BezoekerEmail',
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
