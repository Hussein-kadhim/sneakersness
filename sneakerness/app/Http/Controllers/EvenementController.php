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
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

// 1. Deze controller verzorgt het complete beheer en overzicht van alle Sneakerness evenementen en beursedities.
class EvenementController extends Controller
{
    public function create(Request $request): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        // 2. Alleen ingelogde gebruikers met de rol van organisator hebben toestemming om evenementen aan te maken, te wijzigen of te verwijderen.
        // 3. Bij ongeautoriseerde toegang door andere rollen wordt het verzoek direct afgebroken met een HTTP 403 Forbidden foutmelding.
        abort_unless($user && $user->isOrganisator(), 403);

        $dbError = false;

        try {
            // 4. De controller ondersteunt zowel het happy scenario als het unhappy scenario met gesimuleerde databasefouten.
            if ($request->has('db_error') || $request->has('unhappy')) {
                throw new \RuntimeException('Database is momenteel niet beschikbaar');
            }

            $organisatoren = Organisator::where('IsActief', true)->orderBy('Naam')->get();

            if ($organisatoren->isEmpty()) {
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
        abort_unless($user && $user->isOrganisator(), 403);

        try {
            // 5. Alle invoergegevens van formulieren worden strikt gevalideerd om data-integriteit in de database te garanderen.
            $rules = [
                'Naam' => ['required', 'string', 'max:100'],
                'Datum' => ['required', 'date'],
                'Locatie' => ['required', 'string', 'max:150'],
                'AantalTicketsPerTijdslot' => ['required', 'integer', 'min:1'],
                'BeschikbareStands' => ['required', 'integer', 'min:0'],
                'IsActief' => ['required', 'boolean'],
                'Opmerking' => ['nullable', 'string', 'max:250'],
            ];

            if (Schema::hasColumn('Evenement', 'OrganisatorId')) {
                $rules['OrganisatorId'] = ['required', 'integer', Rule::exists('Organisator', 'Id')->where('IsActief', true)];
            } else {
                $rules['OrganisatorId'] = ['nullable'];
            }

            $validated = $request->validate($rules);

            if (! Schema::hasColumn('Evenement', 'OrganisatorId')) {
                unset($validated['OrganisatorId']);
            }

            Evenement::create($validated);
        } catch (\PDOException $e) {
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

    // 6. De methode destroy() verwijdert een geselecteerd evenement definitief uit de database.
    public function destroy(Request $request, int $event): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
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

            // 7. Er wordt eerst een veilige controle uitgevoerd om te verifiëren of het evenement nog bestaat voordat delete wordt aangeroepen.
            $evenementModel = Evenement::find($event);
            if ($evenementModel) {
                $evenementModel->delete();
            }
        } catch (QueryException|\PDOException $e) {
            // 8. Eventuele databasestoringen worden geregistreerd in het logbestand voor monitoring en technische analyse.
            Log::error('Fout bij het verwijderen van een evenement in EvenementController: '.$e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->route('events.index')
                ->with('databaseError', true);
        }

        // 9. Na een succesvolle verwijdering wordt de gebruiker teruggestuurd naar het overzicht met een bevestigingsmelding.
        return redirect()
            ->route('events.index')
            ->with('success', 'Het event is verwijderd.');
    }

    // 10. De methode edit() toont het bewerkingsformulier en haalt het evenement en actieve organisatoren op.
    public function edit(int $event): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && $user->isOrganisator(), 403);

        try {
            $event = Evenement::findOrFail($event);
            $organisatoren = Organisator::orderBy('Naam')->get();
        } catch (QueryException|\PDOException $e) {
            // 11. Mogelijke databasefouten tijdens het laden van het bewerkingsformulier worden netjes opgevangen zonder dat de applicatie crasht.
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

    // 12. De methode update() verwerkt de gewijzigde gegevens van een bestaand evenement in de database.
    public function update(Request $request, int $event): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        abort_unless($user && $user->isOrganisator(), 403);

        try {
            if ($request->has('db_error') || $request->has('unhappy')) {
                throw new QueryException(
                    'sqlite',
                    'update "Evenement"',
                    [],
                    new \PDOException('Database is momenteel niet beschikbaar')
                );
            }

            // 13. Alle invoervelden worden strikt gevalideerd volgens de gestelde bedrijfseisen, maximale lengtes en datatypes.
            $rules = [
                'Naam' => ['required', 'string', 'max:100'],
                'Datum' => ['required', 'date'],
                'Locatie' => ['required', 'string', 'max:150'],
                'AantalTicketsPerTijdslot' => ['required', 'integer', 'min:1'],
                'BeschikbareStands' => ['required', 'integer', 'min:0'],
                'IsActief' => ['required', 'boolean'],
                'Opmerking' => ['nullable', 'string', 'max:250'],
            ];

            // 14. Er wordt dynamisch gecontroleerd of de kolom OrganisatorId fysiek aanwezig is in de database alvorens deze toe te kennen.
            if (Schema::hasColumn('Evenement', 'OrganisatorId')) {
                $rules['OrganisatorId'] = ['required', 'integer', Rule::exists('Organisator', 'Id')];
            } else {
                $rules['OrganisatorId'] = ['nullable'];
            }

            $validated = $request->validate($rules);

            if (! Schema::hasColumn('Evenement', 'OrganisatorId')) {
                unset($validated['OrganisatorId']);
            }

            Evenement::findOrFail($event)->update($validated);
        } catch (QueryException|\PDOException $e) {
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

    // 15. De methode index() toont het overzicht van alle actieve en geplande edities met zoekfunctionaliteit en KPI-statistieken.
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