<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/**
 * Controller voor het beheren en weergeven van het Stand Overzicht.
 */
class StandController extends Controller
{
    /**
     * Toont het overzicht van alle stands inclusief statistieken, zoekfunctionaliteit en paginering.
     *
     * @param Request $request Bevat eventuele zoek- en filterparameters (q, category, error/unhappy, empty)
     * @return View De gerenderde Blade-weergave met stands en statistieken
     */
    public function index(Request $request): View
    {
        // Ophalen en opschonen van eventuele zoektermen en categoriefilters
        $search = trim($request->input('q', ''));
        $selectedCategory = trim($request->input('category', ''));
        $errorMessage = null;

        // Simulatie van een foutmelding / unhappy path (bijvoorbeeld via ?error=1 of ?unhappy=1 in de URL)
        if ($request->has('error') || $request->has('unhappy')) {
            $errorMessage = 'Database is momenteel niet beschikbaar, de stands konden niet worden geladen. Probeer het later opnieuw.';
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
            ]);
        }

        try {
            // Statistieken berekenen voor de KPI-kaarten bovenaan de pagina
            $totalStands = Stand::count();
            $countAAPlus = Stand::where('StandType', 'AA+')->count();
            $countAA = Stand::where('StandType', 'AA')->count();
            $countA = Stand::where('StandType', 'A')->count();
            $rentedStandsCount = Stand::where('VerhuurdStatus', 1)->count();
            $availableStandsCount = Stand::where('VerhuurdStatus', 0)->count();

            // Basisquery opbouwen met eager loading van de gekoppelde verkoper
            $query = Stand::query()->with('verkoper');

            // Mogelijkheid om een lege lijst te simuleren (?empty=1)
            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

            // Zoekfunctionaliteit op standtype, opmerking/locatie, prijs en verkopergegevens
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

            // Filteren op categorie/status (bijv. verhuurd, beschikbaar, of specifiek standtype)
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

            // Resultaten sorteren op stand ID en pagineren (6 per pagina) met behoud van query parameters
            $stands = $query->orderBy('Id', 'asc')->paginate(6)->withQueryString();

            // View retourneren met alle data en statistieken
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
                'errorMessage'
            ));
        } catch (\Throwable $e) {
            // Foutafhandeling indien de database niet bereikbaar is
            $errorMessage = 'Database is momenteel niet beschikbaar, de stands konden niet worden geladen. Probeer het later opnieuw.';

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
            ]);
        }
    }
}

