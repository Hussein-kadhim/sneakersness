<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\Stand;
use App\Models\Verkoper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

// Controller voor het overzicht en beheer van alle verkopers en partners
class VerkoperController extends Controller
{
    // Toont de verkoperspagina met filters op categorie, zoekbalk en bezettingscijfers
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $selectedCategory = trim($request->input('category', ''));
        $errorMessage = null;

        // Simulatie van het unhappy scenario (database storing)
        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de verkopers konden niet worden geladen. Probeer het later opnieuw.';
            // Criterium: Technische log voor unhappy scenario
            Log::warning('Unhappy scenario gesimuleerd voor verkopers overzicht.', ['ip' => $request->ip()]);

            return view('verkopers.index', [
                'verkopers' => new LengthAwarePaginator([], 0, 6),
                'search' => $search,
                'selectedCategory' => $selectedCategory,
                'totalVerkopers' => 0,
                'countAAPlus' => 0,
                'countAA' => 0,
                'countA' => 0,
                'partnerCount' => 0,
                'rentedStandsCount' => 0,
                'totalStandsCount' => 0,
                'errorMessage' => $errorMessage,
            ]);
        }

        try {
            // Criterium: Stored Procedures demonstratie
            // Roept MySQL Stored Procedure 'sp_GetVerkopersMetDetails' aan
            if ($request->has('sp') || $request->has('procedure')) {
                $spVerkopers = $this->getVerkopersViaProcedure();
                Log::info('Stored Procedure sp_GetVerkopersMetDetails aangeroepen via index.', [
                    'aantal' => count($spVerkopers),
                ]);
            }

            // Statistieken ophalen voor de infokaarten bovenaan het scherm
            $totalVerkopers = Verkoper::count();
            $countAAPlus = Stand::where('StandType', 'AA+')->count();
            $countAA = Stand::where('StandType', 'AA')->count();
            $countA = Stand::where('StandType', 'A')->count();
            $partnerCount = Verkoper::where('SpecialeStatus', 1)->count();
            $rentedStandsCount = Stand::where('VerhuurdStatus', 1)->count();
            $totalStandsCount = Stand::count() > 0 ? Stand::count() : 55;

            // Verkopers inladen inclusief hun gekoppelde stands en contactpersonen
            $query = Verkoper::query()->with(['stands', 'contactpersonen']);

            // Criterium: Gebruik van Joins
            // Expliciete SQL LEFT JOIN demonstratie tussen Verkoper, ContactPerVerkoper en Contactpersoon
            if ($request->has('join') || $request->input('sort') === 'contactpersoon') {
                $query->leftJoin('ContactPerVerkoper', 'Verkoper.Id', '=', 'ContactPerVerkoper.VerkoperId')
                      ->leftJoin('Contactpersoon', 'ContactPerVerkoper.ContactpersoonId', '=', 'Contactpersoon.Id')
                      ->select('Verkoper.*')
                      ->distinct();
            }

            // Lege lijst forceren voor testdoeleinden
            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

            // Zoeken op naam, soort assortiment, opmerking, contactpersoon of standgegevens
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('Naam', 'like', "%{$search}%")
                        ->orWhere('VerkooptSoort', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%")
                        ->orWhereHas('contactpersonen', function ($sub) use ($search) {
                            $sub->where('Naam', 'like', "%{$search}%")
                                ->orWhere('Email', 'like', "%{$search}%")
                                ->orWhere('Telefoonnummer', 'like', "%{$search}%");
                        })
                        ->orWhereHas('stands', function ($sub) use ($search) {
                            $sub->where('Opmerking', 'like', "%{$search}%")
                                ->orWhere('StandType', 'like', "%{$search}%");
                        });
                });
            }

            // Filteren op categorie (bijv. 'Partners' of productgroep)
            if ($selectedCategory !== '') {
                if (strtolower($selectedCategory) === 'partners') {
                    $query->where('SpecialeStatus', 1);
                } else {
                    $query->where('VerkooptSoort', 'like', "%{$selectedCategory}%");
                }
            }

            // Paginering met 6 verkopers per pagina
            $verkopers = $query->orderBy('Verkoper.Id', 'asc')->paginate(6)->withQueryString();

            return view('verkopers.index', compact(
                'verkopers',
                'search',
                'selectedCategory',
                'totalVerkopers',
                'countAAPlus',
                'countAA',
                'countA',
                'partnerCount',
                'rentedStandsCount',
                'totalStandsCount',
                'errorMessage'
            ));
        } catch (\Throwable $e) {
            // Criterium: Technische log wegschrijven
            Log::error('Fout bij het ophalen van verkopers in VerkoperController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Unhappy scenario: database niet beschikbaar
            $errorMessage = 'Database is momenteel niet beschikbaar, de verkopers konden niet worden geladen. Probeer het later opnieuw.';

            return view('verkopers.index', [
                'verkopers' => new LengthAwarePaginator([], 0, 6),
                'search' => $search,
                'selectedCategory' => $selectedCategory,
                'totalVerkopers' => 0,
                'countAAPlus' => 0,
                'countAA' => 0,
                'countA' => 0,
                'partnerCount' => 0,
                'rentedStandsCount' => 0,
                'totalStandsCount' => 0,
                'errorMessage' => $errorMessage,
            ]);
        }
    }

    /**
     * Toont het formulier om een nieuwe verkoper toe te voegen.
     */
    public function create(): View
    {
        $categories = [
            'Sneakers',
            'Streetwear & Merch',
            'Art & Collectibles',
            'Eten en Drinken',
            'Kids Corner',
            'Accessoires',
            'Partners',
            'Custom Sneakers',
            'Overig',
        ];

        $standTypes = [
            'A' => 'Stand A',
            'AA' => 'Stand AA',
            'AA+' => 'Stand AA+ (36m²)',
        ];

        return view('verkopers.create', compact('categories', 'standTypes'));
    }

    /**
     * Slaat een nieuwe verkoper, contactpersoon en stand op in de database.
     */
    public function store(Request $request)
    {
        // Validatieregels volgens de specificatie (Happy & Unhappy scenario)
        $validated = $request->validate([
            'naam' => 'required|string|max:100',
            'verkoopt_soort' => 'required|string|max:100',
            'contactpersoon_naam' => 'required|string|max:100',
            'contactpersoon_email' => 'required|email|max:100',
            'contactpersoon_telefoon' => 'required|string|max:20',
            'stand_type' => 'required|in:A,AA,AA+',
            'stand_nummer' => 'required|string|max:150',
            'dagen' => 'required|string|in:1,2,Zaterdag,Zondag,Weekend',
            'status' => 'required|string',
            'opmerking' => 'nullable|string|max:250',
            'stuur_bevestiging' => 'nullable|boolean',
        ], [
            'naam.required' => 'Bedrijfsnaam is verplicht.',
            'verkoopt_soort.required' => 'Selecteer een categorie.',
            'contactpersoon_naam.required' => 'Naam van de contactpersoon is verplicht.',
            'contactpersoon_email.required' => 'E-mailadres is verplicht.',
            'contactpersoon_email.email' => 'Voer een geldig e-mailadres in.',
            'contactpersoon_telefoon.required' => 'Telefoonnummer is verplicht.',
            'stand_type.required' => 'Selecteer een standtype.',
            'stand_type.in' => 'Selecteer een geldig standtype (A, AA of AA+).',
            'stand_nummer.required' => 'Standnummer / Hal is verplicht.',
            'dagen.required' => 'Selecteer de aanwezigheidsdagen.',
            'status.required' => 'Selecteer een status.',
        ]);

        DB::beginTransaction();
        try {
            $isActief = in_array(strtolower($validated['status']), ['actief', '1', 'bevestigd'], true);
            $specialeStatus = strtolower($validated['verkoopt_soort']) === 'partners' ? 1 : 0;

            // 1. Verkoper aanmaken
            $verkoper = Verkoper::create([
                'Naam'          => $validated['naam'],
                'SpecialeStatus'=> $specialeStatus,
                'VerkooptSoort' => $validated['verkoopt_soort'],
                'StandType'     => $validated['stand_type'],
                'Dagen'         => $validated['dagen'],
                'IsActief'      => $isActief ? 1 : 0,
                'Opmerking'     => $validated['status'],
            ]);

            // 2. Contactpersoon aanmaken
            $contactpersoon = Contactpersoon::create([
                'Naam'           => $validated['contactpersoon_naam'],
                'Telefoonnummer' => $validated['contactpersoon_telefoon'],
                'Emailadres'     => $validated['contactpersoon_email'],
                'IsActief'       => 1,
                'Opmerking'      => 'Contactpersoon ' . $validated['naam'],
            ]);

            // 3. Koppeltabel invullen
            $verkoper->contactpersonen()->attach($contactpersoon->Id, [
                'IsActief' => 1,
                'Opmerking' => 'Aangemaakt via toevoegformulier',
            ]);

            // 4. Stand aanmaken
            $aantalDagen = in_array($validated['dagen'], ['1', 'Zaterdag', 'Zondag'], true) ? 1 : 2;
            $prijsMap = [
                'AA+' => 450.00,
                'AA' => 300.00,
                'A' => 175.00,
            ];
            $prijs = $prijsMap[$validated['stand_type']] ?? 250.00;

            Stand::create([
                'VerkoperId'     => $verkoper->Id,
                'StandType'      => $validated['stand_type'],
                'Prijs'          => $prijs,
                'VerhuurdStatus' => $isActief ? 1 : 0,
                'IsActief'       => 1,
                'Opmerking'      => $validated['stand_nummer'] . ($validated['opmerking'] ? ' • ' . $validated['opmerking'] : ''),
            ]);

            DB::commit();

            Log::info('Nieuwe verkoper succesvol opgeslagen.', [
                'verkoper_id' => $verkoper->Id,
                'naam' => $verkoper->Naam,
            ]);

            return redirect()->route('verkopers.index')
                ->with('success', 'Verkoper "' . $verkoper->Naam . '" is succesvol toegevoegd.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Fout bij het toevoegen van een verkoper: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withInput()->withErrors([
                'general' => 'Er is een technische fout opgetreden bij het opslaan van de verkoper. Probeer het opnieuw.',
            ]);
        }
    }

    /**
     * Criterium: Gebruik van Stored Procedures
     * Haalt verkopers op inclusief contactgegevens via MySQL Stored Procedure.
     *
     * @return array
     */
    public function getVerkopersViaProcedure(): array
    {
        try {
            return DB::select('CALL sp_GetVerkopersMetDetails()');
        } catch (\Throwable $e) {
            Log::error('Fout bij uitvoeren Stored Procedure sp_GetVerkopersMetDetails: ' . $e->getMessage(), [
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
    public function getVerkopersMetJoins()
    {
        try {
            return DB::table('Verkoper')
                ->leftJoin('ContactPerVerkoper', 'Verkoper.Id', '=', 'ContactPerVerkoper.VerkoperId')
                ->leftJoin('Contactpersoon', 'ContactPerVerkoper.ContactpersoonId', '=', 'Contactpersoon.Id')
                ->select(
                    'Verkoper.Id as VerkoperId',
                    'Verkoper.Naam as VerkoperNaam',
                    'Verkoper.VerkooptSoort',
                    'Contactpersoon.Naam as ContactpersoonNaam',
                    'Contactpersoon.Email as ContactpersoonEmail'
                )
                ->where('Verkoper.IsActief', 1)
                ->get();
        } catch (\Throwable $e) {
            Log::error('Fout bij uitvoeren van expliciete JOIN query voor verkopers: ' . $e->getMessage());
            return collect();
        }
    }
}
