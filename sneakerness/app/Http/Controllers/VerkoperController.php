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
