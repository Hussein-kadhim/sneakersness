<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use App\Models\Organisator;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

// Controller voor het beheren en bekijken van Sneakerness edities en evenementen
class EvenementController extends Controller
{
    public function create(Request $request): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        // Alleen organisatoren mogen nieuwe evenementen aanmaken.
        abort_unless($user && $user->isOrganisator(), 403);

        $dbError = false;

        try {
            // Deze parameters maken het mogelijk om de foutweergave gecontroleerd te testen.
            if ($request->has('db_error') || $request->has('unhappy')) {
                throw new \RuntimeException('Database is momenteel niet beschikbaar');
            }

            // Sorteer de keuzelijst en bied alleen actieve organisatoren aan.
            $organisatoren = Organisator::where('IsActief', true)->orderBy('Naam')->get();

            if ($organisatoren->isEmpty()) {
                // Zorg voor een standaardorganisator wanneer er nog geen beschikbaar is.
                $organisator = Organisator::firstOrCreate(
                    ['Gebruikersnaam' => 'organisator'],
                    [
                        'Naam' => 'Sneakerness Events B.V.',
                        'Wachtwoord' => bcrypt('password'),
                        'IsActief' => true,
                        'Opmerking' => 'Hoofdorganisator Sneakerness Rotterdam',
                    ]
                );
                $organisatoren = collect([$organisator]);
            }
        } catch (Throwable $e) {
            // Log de technische fout en toon het formulier met een duidelijke foutstatus.
            Log::error('Fout bij het laden van het eventformulier in EvenementController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $dbError = true;
            $organisatoren = collect();
        }

        return view('evenementen.create', compact('organisatoren', 'dbError'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        // De route is ingelogd, maar aanmaken blijft beperkt tot de organisatorenrol.
        abort_unless($user && $user->isOrganisator(), 403);

        try {
            // Valideer invoer op basis van de kolommen en grenzen van het evenementformulier.
            $validated = $request->validate([
                'OrganisatorId' => ['required', 'integer', Rule::exists('Organisator', 'Id')->where('IsActief', true)],
                'Naam' => ['required', 'string', 'max:100'],
                'Datum' => ['required', 'date'],
                'Locatie' => ['required', 'string', 'max:150'],
                'AantalTicketsPerTijdslot' => ['required', 'integer', 'min:1'],
                'BeschikbareStands' => ['required', 'integer', 'min:0'],
                'IsActief' => ['required', 'boolean'],
                'Opmerking' => ['nullable', 'string', 'max:250'],
            ]);

            // Sla uitsluitend de gevalideerde formulierwaarden op.
            Evenement::create($validated);
        } catch (\PDOException $e) {
            // Keer bij een databasefout terug naar het formulier met een foutmelding.
            Log::error('Fout bij het opslaan van een evenement in EvenementController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->route('events.create')
                ->with('databaseError', true);
        }

        return redirect()
            ->route('events.index')
            ->with('success', 'Het event is toegevoegd.');
    }

    // Laadt het evenement en de organisatoren voor het wijzigingsformulier.
    public function edit(int $event): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        // Alleen een organisator kan de gegevens van een evenement openen om ze te wijzigen.
        abort_unless($user && $user->isOrganisator(), 403);

        try {
            // Haal het gevraagde evenement en de keuzelijst voor de organisator op.
            $event = Evenement::findOrFail($event);
            $organisatoren = Organisator::orderBy('Naam')->get();
        } catch (QueryException|\PDOException $e) {
            // Geef de view een expliciete foutstatus als het laden uit de database mislukt.
            Log::error('Fout bij het laden van het eventformulier in EvenementController: '.$e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('evenementen.edit', [
                'event' => null,
                'organisatoren' => collect(),
                'dbError' => true,
            ]);
        }

        return view('evenementen.edit', [
            'event' => $event,
            'organisatoren' => $organisatoren,
            'dbError' => false,
        ]);
    }

    // Slaat gevalideerde wijzigingen op; een lege Opmerking verwijdert de bestaande omschrijving.
    public function update(Request $request, int $event): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        // Alleen organisatoren mogen evenementgegevens aanpassen.
        abort_unless($user && $user->isOrganisator(), 403);

        try {
            // Simuleer een databasefout zodat de foutafhandeling getest kan worden.
            if ($request->has('db_error') || $request->has('unhappy')) {
                throw new QueryException(
                    'sqlite',
                    'update "Evenement"',
                    [],
                    new \PDOException('Database is momenteel niet beschikbaar')
                );
            }

            // Valideer alle velden opnieuw; Opmerking mag leeg zijn om de omschrijving te wissen.
            $validated = $request->validate([
                'OrganisatorId' => ['required', 'integer', Rule::exists('Organisator', 'Id')],
                'Naam' => ['required', 'string', 'max:100'],
                'Datum' => ['required', 'date'],
                'Locatie' => ['required', 'string', 'max:150'],
                'AantalTicketsPerTijdslot' => ['required', 'integer', 'min:1'],
                'BeschikbareStands' => ['required', 'integer', 'min:0'],
                'IsActief' => ['required', 'boolean'],
                'Opmerking' => ['nullable', 'string', 'max:250'],
            ]);

            // Zoek het evenement op en werk het bij met de gevalideerde waarden.
            Evenement::findOrFail($event)->update($validated);
        } catch (QueryException|\PDOException $e) {
            // Bewaar de invoer na een databasefout zodat de organisator die niet opnieuw hoeft in te vullen.
            Log::error('Fout bij het bijwerken van een evenement in EvenementController: '.$e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->route('events.edit', $event)
                ->withInput($request->except(['_token', '_method']))
                ->with('databaseError', true);
        }

        return redirect()
            ->route('events.index')
            ->with('success', 'Het event is gewijzigd.');
    }

    // Verwijdert het volledige evenement, niet alleen de omschrijving.
    public function destroy(Request $request, int $event): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        // Verwijderen van evenementen is voorbehouden aan organisatoren.
        abort_unless($user && $user->isOrganisator(), 403);

        try {
            if ($request->has('db_error') || $request->has('unhappy')) {
                throw new QueryException(
                    'sqlite',
                    'delete from "Evenement"',
                    [],
                    new \PDOException('Database is momenteel niet beschikbaar')
                );
            }

            // Verwijder het volledige evenement; de relatie kan gekoppelde tickets ook opruimen.
            Evenement::findOrFail($event)->delete();
        } catch (QueryException|\PDOException $e) {
            Log::error('Fout bij het verwijderen van een evenement in EvenementController: '.$e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->route('events.index')
                ->with('databaseError', true);
        }

        return redirect()
            ->route('events.index')
            ->with('success', 'Het event is verwijderd.');
    }

    // Toont de evenementenpagina met actieve edities, zoekbalk en statistieken
    public function index(Request $request): View
    {
        // Haal witruimte van de zoekterm af zodat lege invoer geen filter activeert.
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
            // Verzamel totalen voor de overzichtskaarten boven de resultaten.
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
                // Zoek op titel, locatie en omschrijving binnen dezelfde samengestelde voorwaarde.
                $query->where(function ($q) use ($search) {
                    $q->where('Naam', 'like', "%{$search}%")
                        ->orWhere('Locatie', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%");
                });
            }

            // Gesorteerd op datum met paginering (8 items per pagina)
            // Houd filters in de URL vast wanneer de gebruiker naar een volgende pagina gaat.
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
            // Geef de resultaten van de vooraf gedefinieerde MySQL-procedure terug.
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
            // Combineer actieve evenementen met hun organisator voor het overzicht.
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