<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\ContactPerVerkoper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

// Controller voor het beheren en inzien van contactpersonen
class ContactpersoonController extends Controller
{
    // Toont de lijst met contactpersonen inclusief zoekfunctie en statistieken
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $errorMessage = null;

        // Simulatie voor het unhappy scenario (database foutmelding)
        if ($request->has('error') || $request->has('unhappy') || $request->has('db_error') || $request->has('database_error')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de contactpersonen konden niet worden geladen. Probeer het later opnieuw.';
            // Criterium: Technische log voor unhappy scenario
            Log::warning('Unhappy scenario gesimuleerd voor contactpersonen overzicht.', ['ip' => $request->ip()]);

            return view('contactpersonen.index', [
                'contactpersonen' => new LengthAwarePaginator([], 0, 6),
                'search' => $search,
                'totalContactpersonen' => 0,
                'linkedCount' => 0,
                'unlinkedCount' => 0,
                'primaryCount' => 0,
                'errorMessage' => $errorMessage,
            ]);
        }

        try {
            // Criterium: Stored Procedures demonstratie
            // Roept MySQL Stored Procedure 'sp_GetContactpersonenMetVerkoper' aan
            if ($request->has('sp') || $request->has('procedure')) {
                $spContacten = $this->getContactpersonenViaProcedure();
                Log::info('Stored Procedure sp_GetContactpersonenMetVerkoper succesvol aangeroepen via index.', [
                    'aantal' => count($spContacten),
                ]);
            }

            // KPI tellers berekenen voor de statistiekenbalk bovenaan
            $totalContactpersonen = Contactpersoon::count();
            $linkedCount = Contactpersoon::has('verkopers')->count();
            $unlinkedCount = Contactpersoon::doesntHave('verkopers')->count();
            $primaryCount = ContactPerVerkoper::where('Opmerking', 'Hoofdcontactpersoon')->count();

            // Query opbouwen met gekoppelde verkopers en hun stands
            $query = Contactpersoon::query()->with(['verkopers.stands']);

            // Criterium: Gebruik van Joins
            // Expliciete SQL LEFT JOIN demonstratie tussen Contactpersoon, ContactPerVerkoper en Verkoper
            if ($request->has('join') || $request->input('sort') === 'verkoper') {
                $query->leftJoin('ContactPerVerkoper', 'Contactpersoon.Id', '=', 'ContactPerVerkoper.ContactpersoonId')
                      ->leftJoin('Verkoper', 'ContactPerVerkoper.VerkoperId', '=', 'Verkoper.Id')
                      ->select('Contactpersoon.*')
                      ->distinct();
            }

            // Testparameter om een lege lijst te simuleren
            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

            // Zoeken op naam, e-mail, telefoon, functie of gekoppelde verkoper
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('Naam', 'like', "%{$search}%")
                        ->orWhere('Email', 'like', "%{$search}%")
                        ->orWhere('Telefoonnummer', 'like', "%{$search}%")
                        ->orWhere('Opmerking', 'like', "%{$search}%")
                        ->orWhereHas('verkopers', function ($sub) use ($search) {
                            $sub->where('Naam', 'like', "%{$search}%")
                                ->orWhere('VerkooptSoort', 'like', "%{$search}%");
                        });
                });
            }

            // Paginering per 6 items met behoud van queryparameters in de links
            $contactpersonen = $query->orderBy('Contactpersoon.Id', 'asc')->paginate(6)->onEachSide(1)->withQueryString();

            return view('contactpersonen.index', compact(
                'contactpersonen',
                'search',
                'totalContactpersonen',
                'linkedCount',
                'unlinkedCount',
                'primaryCount',
                'errorMessage'
            ));
        } catch (\Throwable $e) {
            // Criterium: Technische log wegschrijven
            Log::error('Fout bij het ophalen van contactpersonen in ContactpersoonController: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Vang onverwachte databasefouten op en toon een vriendelijke melding
            $errorMessage = 'Database is momenteel niet beschikbaar, de contactpersonen konden niet worden geladen. Probeer het later opnieuw.';

            return view('contactpersonen.index', [
                'contactpersonen' => new LengthAwarePaginator([], 0, 6),
                'search' => $search,
                'totalContactpersonen' => 0,
                'linkedCount' => 0,
                'unlinkedCount' => 0,
                'primaryCount' => 0,
                'errorMessage' => $errorMessage,
            ]);
        }
    }

    /**
     * Criterium: Gebruik van Stored Procedures
     * Haalt contactpersonen op inclusief verkopergegevens via MySQL Stored Procedure.
     *
     * @return array
     */
    public function getContactpersonenViaProcedure(): array
    {
        try {
            return DB::select('CALL sp_GetContactpersonenMetVerkoper()');
        } catch (\Throwable $e) {
            Log::error('Fout bij uitvoeren Stored Procedure sp_GetContactpersonenMetVerkoper: ' . $e->getMessage(), [
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
    public function getContactpersonenMetJoins()
    {
        try {
            return DB::table('Contactpersoon')
                ->leftJoin('ContactPerVerkoper', 'Contactpersoon.Id', '=', 'ContactPerVerkoper.ContactpersoonId')
                ->leftJoin('Verkoper', 'ContactPerVerkoper.VerkoperId', '=', 'Verkoper.Id')
                ->select(
                    'Contactpersoon.Id as ContactpersoonId',
                    'Contactpersoon.Naam as ContactpersoonNaam',
                    'Contactpersoon.Email as ContactpersoonEmail',
                    'Contactpersoon.Telefoonnummer',
                    'Verkoper.Naam as VerkoperNaam',
                    'Verkoper.VerkooptSoort'
                )
                ->where('Contactpersoon.IsActief', 1)
                ->get();
        } catch (\Throwable $e) {
            Log::error('Fout bij uitvoeren van expliciete JOIN query voor contactpersonen: ' . $e->getMessage());
            return collect();
        }
    }
}
