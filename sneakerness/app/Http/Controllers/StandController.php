<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

// Controller voor het beheer van stands op de beursvloer
class StandController extends Controller
{
    // Toont het standsoverzicht met filters, zoekfunctionaliteit en bezettingsgraden
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $selectedCategory = trim($request->input('category', ''));
        $errorMessage = null;

        // Simulatie van het unhappy scenario bij databaseproblemen
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
            // Bezettings- en type-statistieken verzamelen voor de tellers
            $totalStands = Stand::count();
            $countAAPlus = Stand::where('StandType', 'AA+')->count();
            $countAA = Stand::where('StandType', 'AA')->count();
            $countA = Stand::where('StandType', 'A')->count();
            $rentedStandsCount = Stand::where('VerhuurdStatus', 1)->count();
            $availableStandsCount = Stand::where('VerhuurdStatus', 0)->count();

            // Query met gekoppelde verkopergegevens inladen
            $query = Stand::query()->with('verkoper');

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
            $stands = $query->orderBy('Id', 'asc')->paginate(6)->withQueryString();

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
            // Nette foutafhandeling met fallback naar een lege paginator
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
