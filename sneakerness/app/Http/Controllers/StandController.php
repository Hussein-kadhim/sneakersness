<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use App\Models\Verkoper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            // Paginering met 6 stands per pagina
            $stands = $query->orderBy('Stand.Id', 'asc')->paginate(6)->withQueryString();

            return view('stands.index', compact(
                'stands',
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
        // =========================================================================
        // DEMO VOOR DOCENT (Unhappy Scenario):
        // Simulatie van het unhappy scenario bij databaseproblemen via URL parameters
        // =========================================================================
        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de gegevens voor stand toevoegen konden niet worden geladen. Probeer het later opnieuw.';
            $dbError = true;

            // Criterium: Technische log voor unhappy scenario
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
            // Criterium: Technische log wegschrijven in de catch
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
        // =========================================================================
        // DEMO VOOR DOCENT (Unhappy Scenario):
        // Simulatie van het unhappy scenario bij databaseproblemen via parameters
        // =========================================================================
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
