<?php

namespace App\Http\Controllers;

use App\Models\Contactpersoon;
use App\Models\Stand;
use App\Models\Verkoper;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerkoperController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('q', ''));
        $selectedCategory = trim($request->input('category', ''));

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
            'totalStandsCount'
        ));
    }
}
