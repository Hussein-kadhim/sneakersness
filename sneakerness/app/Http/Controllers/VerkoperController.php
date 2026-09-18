<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\Stand;
use App\Models\Verkoper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class VerkoperController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $selectedCategory = trim($request->input('category', ''));
        $errorMessage = null;

        if ($request->has('error') || $request->has('unhappy')) {
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

        try {
            $totalVerkopers = Verkoper::count();
            $countAAPlus = Stand::where('StandType', 'AA+')->count();
            $countAA = Stand::where('StandType', 'AA')->count();
            $countA = Stand::where('StandType', 'A')->count();
            $partnerCount = Verkoper::where('SpecialeStatus', 1)->count();
            $rentedStandsCount = Stand::where('VerhuurdStatus', 1)->count();
            $totalStandsCount = Stand::count() > 0 ? Stand::count() : 55;

            $query = Verkoper::query()->with(['stands', 'contactpersonen']);

            if ($request->has('empty')) {
                $query->whereNull('Id');
            }

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

            if ($selectedCategory !== '') {
                if (strtolower($selectedCategory) === 'partners') {
                    $query->where('SpecialeStatus', 1);
                } else {
                    $query->where('VerkooptSoort', 'like', "%{$selectedCategory}%");
                }
            }

            $verkopers = $query->orderBy('Id', 'asc')->paginate(6)->withQueryString();

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
}

