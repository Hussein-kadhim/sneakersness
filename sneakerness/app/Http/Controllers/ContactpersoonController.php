<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\ContactPerVerkoper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
            // KPI tellers berekenen voor de statistiekenbalk bovenaan
            $totalContactpersonen = Contactpersoon::count();
            $linkedCount = Contactpersoon::has('verkopers')->count();
            $unlinkedCount = Contactpersoon::doesntHave('verkopers')->count();
            $primaryCount = ContactPerVerkoper::where('Opmerking', 'Hoofdcontactpersoon')->count();

            // Query opbouwen met gekoppelde verkopers en hun stands
            $query = Contactpersoon::query()->with(['verkopers.stands']);

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
            $contactpersonen = $query->orderBy('Id', 'asc')->paginate(6)->onEachSide(1)->withQueryString();

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
}
