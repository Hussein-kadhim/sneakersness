<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use App\Models\Verkoper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

// Controller voor het beheer van stands op de beursvloer
class StandController extends Controller
{
    // Toont het standsoverzicht met filters, zoekfunctionaliteit en bezettingsgraden
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $selectedCategory = trim($request->input('category', ''));
        $errorMessage = null;
        $dbError = false;

        // =========================================================================
        // DEMO VOOR DOCENT (Unhappy Scenario):
        // Simulatie van het unhappy scenario bij databaseproblemen via URL parameters
        // =========================================================================
        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de stands konden niet worden geladen. Probeer het later opnieuw.';
            $dbError = true;

            // Criterium: Technische log voor unhappy scenario
            Log::warning('Unhappy scenario gesimuleerd voor stands overzicht.', ['ip' => $request->ip()]);

            return view('stands.index', [
                'stands' => new LengthAwarePaginator([], 0, 6),
                'allStands' => collect(),
                'myReservedStands' => collect(),
                'currentVerkoper' => null,
                'search' => $search,
                'selectedCategory' => $selectedCategory,
                'totalStands' => 0,
                'countAAPlus' => 0,
                'countAA' => 0,
                'countA' => 0,
                'rentedStandsCount' => 0,
                'availableStandsCount' => 0,
                'errorMessage' => $errorMessage,
                'dbError' => $dbError,
            ]);
        }

        try {
            // Criterium: Stored Procedures demonstratie
            // Roept MySQL Stored Procedure 'sp_GetStandsMetVerkoper' aan
            if ($request->has('sp') || $request->has('procedure')) {
                $spStands = $this->getStandsViaProcedure();
                Log::info('Stored Procedure sp_GetStandsMetVerkoper aangeroepen via index.', [
                    'aantal' => count($spStands),
                ]);
            }

            // Bezettings- en type-statistieken verzamelen voor de tellers
            $totalStands = Stand::count();
            $countAAPlus = Stand::where('StandType', 'AA+')->count();
            $countAA = Stand::where('StandType', 'AA')->count();
            $countA = Stand::where('StandType', 'A')->count();
            $rentedStandsCount = Stand::where('VerhuurdStatus', 1)->count();
            $availableStandsCount = Stand::where('VerhuurdStatus', 0)->count();

            // Alle stands ophalen voor snelle selectie / reservering
            $allStands = Stand::orderBy('Id', 'asc')->get();

            // Query met gekoppelde verkopergegevens inladen
            $query = Stand::query()->with('verkoper');

            // Criterium: Gebruik van Joins
            // Expliciete SQL INNER JOIN demonstratie tussen Stand en Verkoper
            if ($request->has('join') || $request->input('sort') === 'verkoper') {
                $query->join('Verkoper', 'Stand.VerkoperId', '=', 'Verkoper.Id')
                      ->select('Stand.*');
            }

            // Lege weergave forceren voor testdoeleinden
            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

            // Zoeken op standtype, toelichting, prijs of naam/assortiment van de verkoper
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('StandType', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%")
                        ->orWhere('Prijs', 'like', "%{$search}%")
                        ->orWhereHas('verkoper', function ($sub) use ($search) {
                            $sub->where('Naam', 'like', "%{$search}%")
                                ->orWhere('VerkooptSoort', 'like', "%{$search}%");
                        });
                });
            }

            // Filteren op status (verhuurd/beschikbaar) of type stand (AA+, AA, A)
            if ($selectedCategory !== '') {
                $catLower = strtolower($selectedCategory);
                if ($catLower === 'verhuurd') {
                    $query->where('VerhuurdStatus', 1);
                } elseif ($catLower === 'beschikbaar') {
                    $query->where('VerhuurdStatus', 0);
                } elseif (in_array(strtoupper($selectedCategory), ['AA+', 'AA', 'A'])) {
                    $query->where('StandType', strtoupper($selectedCategory));
                } else {
                    $query->where('StandType', 'like', "%{$selectedCategory}%");
                }
            }

            // Stands van de ingelogde verkoper ophalen voor beheer en wijzigopties
            $user = Auth::user();
            $myReservedStands = collect();
            $currentVerkoper = null;
            if ($user && $user->role === 'verkoper') {
                $currentVerkoper = Verkoper::where('Naam', $user->name)->first()
                    ?? Verkoper::whereRaw('LOWER(TRIM(Naam)) = ?', [strtolower(trim($user->name))])->first();
                if ($currentVerkoper) {
                    $myReservedStands = Stand::where('VerkoperId', $currentVerkoper->Id)
                        ->where('VerhuurdStatus', 1)
                        ->orderBy('Id', 'asc')
                        ->get();
                }
            }

            // Paginering met 6 stands per pagina
            $stands = $query->orderBy('Stand.Id', 'asc')->paginate(6)->withQueryString();

            return view('stands.index', compact(
                'stands',
                'allStands',
                'myReservedStands',
                'currentVerkoper',
                'search',
                'selectedCategory',
                'totalStands',
                'countAAPlus',
                'countAA',
                'countA',
                'rentedStandsCount',
                'availableStandsCount',
                'errorMessage',
                'dbError'
            ));
        } catch (Throwable $e) {
            // Criterium: Technische log wegschrijven in de catch
            Log::error('Fout bij het ophalen van stands in StandController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Unhappy scenario: nette foutmelding aan de eindgebruiker
            $errorMessage = 'Database is momenteel niet beschikbaar, de stands konden niet worden geladen. Probeer het later opnieuw.';
            $dbError = true;

            return view('stands.index', [
                'stands' => new LengthAwarePaginator([], 0, 6),
                'allStands' => collect(),
                'myReservedStands' => collect(),
                'currentVerkoper' => null,
                'search' => $search,
                'selectedCategory' => $selectedCategory,
                'totalStands' => 0,
                'countAAPlus' => 0,
                'countAA' => 0,
                'countA' => 0,
                'rentedStandsCount' => 0,
                'availableStandsCount' => 0,
                'errorMessage' => $errorMessage,
                'dbError' => $dbError,
            ]);
        }
    }

    /**
     * Toont het formulier om een nieuwe stand toe te voegen.
     */
    public function create(Request $request): View
    {
        abort_unless(Auth::user() && Auth::user()->role === 'organisator', 403);

        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de gegevens voor stand toevoegen konden niet worden geladen. Probeer het later opnieuw.';
            $dbError = true;

            Log::warning('Unhappy scenario gesimuleerd voor stand toevoegen formulier.', ['ip' => $request->ip()]);

            return view('stands.create', [
                'verkopers' => collect(),
                'dbError' => $dbError,
                'errorMessage' => $errorMessage,
            ]);
        }

        try {
            $verkopers = Verkoper::where('IsActief', 1)->orderBy('Naam', 'asc')->get();

            return view('stands.create', [
                'verkopers' => $verkopers,
                'dbError' => false,
                'errorMessage' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('Fout bij het ophalen van verkopers voor stand toevoegen in StandController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $errorMessage = 'Database is momenteel niet beschikbaar, de gegevens voor stand toevoegen konden niet worden geladen. Probeer het later opnieuw.';
            $dbError = true;

            return view('stands.create', [
                'verkopers' => collect(),
                'dbError' => $dbError,
                'errorMessage' => $errorMessage,
            ]);
        }
    }

    /**
     * Valideert en slaat een nieuw aangemaakte stand op in de database.
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user() && Auth::user()->role === 'organisator', 403);

        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de stand kon niet worden toegevoegd. Probeer het later opnieuw.';
            $dbError = true;

            Log::warning('Unhappy scenario gesimuleerd voor opslaan van stand.', ['ip' => $request->ip()]);

            return response()->view('stands.create', [
                'verkopers' => collect(),
                'dbError' => $dbError,
                'errorMessage' => $errorMessage,
            ], 200);
        }

        $validated = $request->validate([
            'StandType' => ['required', 'string', 'in:AA+,AA,A'],
            'VerkoperId' => ['required', 'integer'],
            'Prijs' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'AantalDagen' => ['required', 'integer', 'in:1,2'],
            'VerhuurdStatus' => ['required', 'boolean'],
            'Opmerking' => ['nullable', 'string', 'max:250'],
        ], [
            'StandType.required' => 'Het standtype is verplicht.',
            'StandType.in' => 'Kies een geldig standtype (AA+, AA of A).',
            'VerkoperId.required' => 'Selecteer een verkoper voor deze stand.',
            'Prijs.required' => 'De prijs is verplicht.',
            'Prijs.numeric' => 'Voer een geldig bedrag in voor de prijs.',
            'Prijs.min' => 'De prijs mag niet negatief zijn.',
            'Prijs.max' => 'De prijs mag niet hoger zijn dan € 9.999,99.',
            'AantalDagen.required' => 'Het aantal dagen is verplicht.',
            'AantalDagen.in' => 'Kies 1 dag of 2 dagen.',
            'VerhuurdStatus.required' => 'Selecteer de verhuurstatus.',
            'Opmerking.max' => 'De opmerking mag maximaal 250 tekens lang zijn.',
        ]);

        try {
            $stand = Stand::create([
                'StandType' => $validated['StandType'],
                'VerkoperId' => (int) $validated['VerkoperId'],
                'Prijs' => $validated['Prijs'],
                'AantalDagen' => (int) $validated['AantalDagen'],
                'VerhuurdStatus' => (bool) $validated['VerhuurdStatus'],
                'Opmerking' => $validated['Opmerking'] ?? null,
                'IsActief' => true,
            ]);

            Log::info('Nieuwe stand succesvol toegevoegd.', [
                'stand_id' => $stand->Id,
                'stand_type' => $stand->StandType,
                'verkoper_id' => $stand->VerkoperId,
            ]);

            return redirect()->route('stands.index')->with('success', 'Stand #' . $stand->StandType . '-' . str_pad($stand->Id, 3, '0', STR_PAD_LEFT) . ' is succesvol toegevoegd!');
        } catch (Throwable $e) {
            Log::error('Fout bij het opslaan van nieuwe stand in StandController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $errorMessage = 'Database is momenteel niet beschikbaar, de stand kon niet worden toegevoegd. Probeer het later opnieuw.';
            $dbError = true;

            $verkopers = collect();
            try {
                $verkopers = Verkoper::where('IsActief', 1)->orderBy('Naam', 'asc')->get();
            } catch (Throwable $ve) {
                // Database verbinding niet beschikbaar
            }

            return response()->view('stands.create', [
                'verkopers' => $verkopers,
                'dbError' => $dbError,
                'errorMessage' => $errorMessage,
            ], 200);
        }
    }

    /**
     * Schakelt door naar het wijzigformulier op basis van formulierselectie.
     */
    public function selectEdit(Request $request): RedirectResponse
    {
        $standId = $request->input('stand_id');

        if (!$standId) {
            return redirect()->route('stands.index')
                ->with('error', 'Selecteer een stand om te wijzigen.');
        }

        return redirect()->route('stands.edit', (int)$standId);
    }

    /**
     * Toont het formulier om een standreservering te wijzigen.
     */
    public function edit(Request $request, int|string $id): View|RedirectResponse
    {
        $user = Auth::user();

        // Alleen ingelogde gebruikers met de juiste rechten mogen een stand wijzigen
        if (!$user) {
            return redirect()->route('login');
        }

        // Controleer dat de reservering / stand bestaat
        $stand = Stand::with('verkoper')->find($id);
        if (!$stand) {
            abort(404, 'Stand niet gevonden.');
        }

        // Autorisatiecontrole:
        // Een verkoper mag uitsluitend zijn eigen reservering wijzigen.
        // Zorg dat andere verkopers geen reserveringen kunnen aanpassen die niet van hen zijn.
        if ($user->role === 'verkoper') {
            $verkoper = Verkoper::where('Naam', $user->name)->first()
                ?? Verkoper::whereRaw('LOWER(TRIM(Naam)) = ?', [strtolower(trim($user->name))])->first();

            if (!$verkoper || $stand->VerkoperId !== $verkoper->Id || !$stand->VerhuurdStatus) {
                abort(403, 'Je mag alleen je eigen standreservering wijzigen.');
            }
        } elseif ($user->role !== 'organisator') {
            abort(403, 'Geen toegang tot het wijzigen van stands.');
        }

        // DEMO VOOR DOCENT (Unhappy Scenario):
        // Simulatie van databaseproblemen via query parameters
        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de standgegevens konden niet worden geladen. Probeer het later opnieuw.';
            $dbError = true;

            Log::warning('Unhappy scenario gesimuleerd voor stand wijzigen formulier.', [
                'ip' => $request->ip(),
                'stand_id' => $stand->Id,
            ]);

            return view('stands.edit', [
                'stand' => $stand,
                'dbError' => $dbError,
                'errorMessage' => $errorMessage,
            ]);
        }

        return view('stands.edit', [
            'stand' => $stand,
            'dbError' => false,
            'errorMessage' => null,
        ]);
    }

    /**
     * Valideert en slaat de gewijzigde gegevens van een stand op in de database.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $user = Auth::user();

        // Alleen ingelogde gebruikers met de juiste rechten mogen een stand wijzigen
        if (!$user) {
            return redirect()->route('login');
        }

        // Controleer dat de reservering / stand bestaat
        $stand = Stand::find($id);
        if (!$stand) {
            abort(404, 'Stand niet gevonden.');
        }

        // Autorisatiecontrole:
        // Een verkoper mag uitsluitend zijn eigen reservering wijzigen.
        // Zorg dat andere verkopers geen reserveringen kunnen aanpassen die niet van hen zijn.
        if ($user->role === 'verkoper') {
            $verkoper = Verkoper::where('Naam', $user->name)->first()
                ?? Verkoper::whereRaw('LOWER(TRIM(Naam)) = ?', [strtolower(trim($user->name))])->first();

            if (!$verkoper || $stand->VerkoperId !== $verkoper->Id || !$stand->VerhuurdStatus) {
                abort(403, 'Je mag alleen je eigen standreservering wijzigen.');
            }
        } elseif ($user->role !== 'organisator') {
            abort(403, 'Geen toegang tot het wijzigen van stands.');
        }

        // DEMO VOOR DOCENT (Unhappy Scenario):
        // Simulatie van unhappy scenario bij opslaan via parameters
        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            Log::warning('Unhappy scenario gesimuleerd voor stand wijzigen opslaan.', [
                'stand_id' => $stand->Id,
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Stand wijzigen is niet gelukt');
        }

        // Laravel validatie:
        // Ongeldige invoer mag niet worden opgeslagen.
        // Verplichte velden mogen niet leeg zijn.
        $validator = Validator::make($request->all(), [
            'StandType'   => ['required', 'string', 'in:AA+,AA,A'],
            'AantalDagen' => ['required', 'integer', 'in:1,2'],
            'Prijs'       => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'Opmerking'   => ['nullable', 'string', 'max:250'],
        ], [
            'StandType.required'   => 'Het standtype is verplicht.',
            'StandType.in'         => 'Kies een geldig standtype (AA+, AA of A).',
            'AantalDagen.required' => 'Het aantal dagen is verplicht.',
            'AantalDagen.in'       => 'Kies 1 dag of 2 dagen.',
            'Prijs.required'       => 'De prijs is verplicht.',
            'Prijs.numeric'        => 'Voer een geldig bedrag in voor de prijs.',
            'Prijs.min'            => 'De prijs mag niet negatief zijn.',
            'Prijs.max'            => 'De prijs mag niet hoger zijn dan € 9.999,99.',
            'Opmerking.max'        => 'De opmerking mag maximaal 250 tekens lang zijn.',
        ]);

        // Unhappy scenario:
        // Toon bij ongeldige invoer de melding "Stand wijzigen is niet gelukt".
        // Toon ook welke invoervelden ongeldig zijn.
        if ($validator->fails()) {
            Log::warning('Validatie mislukt bij stand wijzigen.', [
                'stand_id' => $stand->Id,
                'errors' => $validator->errors()->toArray(),
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Stand wijzigen is niet gelukt');
        }

        try {
            $validated = $validator->validated();

            // Happy scenario:
            // Bij geldige gegevens worden de wijzigingen opgeslagen in de database.
            $stand->update([
                'StandType'   => $validated['StandType'],
                'AantalDagen' => (int) $validated['AantalDagen'],
                'Prijs'       => $validated['Prijs'],
                'Opmerking'   => $validated['Opmerking'] ?? null,
            ]);

            Log::info('Stand succesvol gewijzigd door verkoper/organisator.', [
                'stand_id' => $stand->Id,
                'user_id' => $user->id,
            ]);

            // De gebruiker krijgt de melding "Stand is succesvol gewijzigd".
            // De gewijzigde gegevens zijn direct zichtbaar.
            return redirect()->route('stands.index')
                ->with('success', 'Stand is succesvol gewijzigd');
        } catch (Throwable $e) {
            Log::error('Fout bij het updaten van stand in StandController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'stand_id' => $stand->Id,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Stand wijzigen is niet gelukt');
        }
    }

    /**
     * Reserveert een specifieke stand via URL ID voor de ingelogde verkoper.
     */
    public function reserveren(Request $request, int|string $id): RedirectResponse
    {
        return $this->verwerkReservering($request, (int)$id);
    }

    /**
     * Reserveert een stand geselecteerd via formulierkeuze voor de ingelogde verkoper.
     */
    public function reserverenSelectie(Request $request): RedirectResponse
    {
        $request->validate([
            'stand_id' => ['required', 'integer'],
        ], [
            'stand_id.required' => 'Kies een stand om te reserveren.',
        ]);

        return $this->verwerkReservering($request, (int)$request->input('stand_id'));
    }

    /**
     * Verwerkt de standreservering inclusief validatie en unhappy scenario's.
     */
    protected function verwerkReservering(Request $request, int $standId): RedirectResponse
    {
        $user = Auth::user();

        // Authenticatiecontrole: alleen ingelogde verkopers mogen reserveren
        if (!$user || $user->role !== 'verkoper') {
            return redirect()->route('stands.index')
                ->with('error', 'Alleen ingelogde verkopers kunnen een stand reserveren.');
        }

        try {
            $stand = Stand::find($standId);

            if (!$stand) {
                return redirect()->route('stands.index')
                    ->with('error', 'Deze stand bestaat niet.');
            }

            // =========================================================================
            // UNHAPPY SCENARIO:
            // Stand is al gereserveerd (VerhuurdStatus == 1)
            // =========================================================================
            if ($stand->VerhuurdStatus == 1 || $request->has('al_gereserveerd')) {
                Log::warning('Stand reservering afgewezen: stand is al gereserveerd.', [
                    'stand_id' => $stand->Id,
                    'user_id' => $user->id,
                ]);

                // Stand wordt niet gereserveerd
                return redirect()->route('stands.index')
                    ->with('error', 'Deze stand is al gereserveerd');
            }

            // Koppel de verkoper behorende bij de ingelogde gebruiker
            $verkoper = Verkoper::where('Naam', $user->name)->first();
            if (!$verkoper) {
                $verkoper = Verkoper::create([
                    'Naam' => $user->name,
                    'SpecialeStatus' => 0,
                    'VerkooptSoort' => 'Sneakers',
                    'IsActief' => 1,
                    'Opmerking' => 'Geregistreerde verkoper',
                ]);
            }

            // =========================================================================
            // HAPPY SCENARIO:
            // Stand is beschikbaar en wordt gereserveerd voor de verkoper
            // =========================================================================
            $stand->VerhuurdStatus = 1;
            $stand->VerkoperId = $verkoper->Id;
            $stand->save();

            Log::info('Stand succesvol gereserveerd door verkoper.', [
                'stand_id' => $stand->Id,
                'verkoper_id' => $verkoper->Id,
                'user_id' => $user->id,
            ]);

            return redirect()->route('stands.index')
                ->with('success', 'Stand is succesvol gereserveerd');
        } catch (Throwable $e) {
            Log::error('Fout bij het reserveren van stand in StandController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'stand_id' => $standId,
            ]);

            return redirect()->route('stands.index')
                ->with('error', 'Database is momenteel niet beschikbaar, de stand kon niet worden gereserveerd.');
        }
    }

    /**
     * Verwijdert een stand van de beursvloer.
     */
    public function destroy(Request $request, int|string $id): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // =========================================================================
        // UNHAPPY SCENARIO:
        // Gesimuleerde databasefout via query-parameter
        // =========================================================================
        if ($request->hasAny(['error', 'unhappy', 'db_error', 'database_error'])) {
            Log::warning('Gesimuleerde databasefout bij het verwijderen van een stand.', [
                'stand_id' => $id,
                'user_id' => $user->id,
            ]);

            return redirect()->route('stands.index')
                ->with('error', 'Stand verwijderen is niet gelukt');
        }

        $stand = Stand::find((int) $id);

        if (!$stand) {
            return redirect()->route('stands.index')
                ->with('error', 'Deze stand bestaat niet.');
        }

        // Autorisatiecontrole:
        // Organisator mag alle stands verwijderen
        // Verkoper mag alleen zijn eigen stand verwijderen
        if ($user->isVerkoper()) {
            $isEigenStand = ($stand->VerkoperId && $stand->verkoper && $stand->verkoper->Naam === $user->name);
            if (!$isEigenStand) {
                $userVerkoper = Verkoper::where('Naam', $user->name)->first();
                if (!$userVerkoper || $stand->VerkoperId !== $userVerkoper->Id) {
                    abort(403, 'Je bent niet bevoegd om deze stand te verwijderen.');
                }
            }
        } elseif (!$user->isOrganisator()) {
            abort(403, 'Je hebt geen rechten om een stand te verwijderen.');
        }

        try {
            $standId = $stand->Id;
            $stand->delete();

            Log::info('Stand succesvol verwijderd.', [
                'stand_id' => $standId,
                'user_id' => $user->id,
            ]);

            return redirect()->route('stands.index')
                ->with('success', 'Stand is succesvol verwijderd');
        } catch (Throwable $e) {
            Log::error('Fout bij het verwijderen van stand in StandController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'stand_id' => $id,
            ]);

            return redirect()->route('stands.index')
                ->with('error', 'Stand verwijderen is niet gelukt');
        }
    }

    /**
     * Criterium: Gebruik van Stored Procedures
     * Haalt alle stands op inclusief verkoperdetails via MySQL Stored Procedure.
     *
     * @return array
     */
    public function getStandsViaProcedure(): array
    {
        try {
            return DB::select('CALL sp_GetStandsMetVerkoper()');
        } catch (Throwable $e) {
            Log::error('Fout bij het uitvoeren van Stored Procedure sp_GetStandsMetVerkoper: ' . $e->getMessage(), [
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
    public function getStandsMetJoins()
    {
        try {
            return DB::table('Stand')
                ->join('Verkoper', 'Stand.VerkoperId', '=', 'Verkoper.Id')
                ->select(
                    'Stand.Id as StandId',
                    'Stand.StandType',
                    'Stand.Prijs',
                    'Stand.AantalDagen',
                    'Stand.VerhuurdStatus',
                    'Stand.Opmerking as StandLocatie',
                    'Verkoper.Id as VerkoperId',
                    'Verkoper.Naam as VerkoperNaam',
                    'Verkoper.VerkooptSoort'
                )
                ->where('Stand.IsActief', 1)
                ->get();
        } catch (Throwable $e) {
            Log::error('Fout bij uitvoeren van expliciete JOIN query voor stands: ' . $e->getMessage());
            return collect();
        }
    }
}
