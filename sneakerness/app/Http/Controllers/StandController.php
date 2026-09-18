<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandController extends Controller
{
    public function index(Request $request): View
    {
        $totalStands = Stand::count();
        $countAAPlus = Stand::where('StandType', 'AA+')->count();
        $countAA = Stand::where('StandType', 'AA')->count();
        $countA = Stand::where('StandType', 'A')->count();
        $rentedStandsCount = Stand::where('VerhuurdStatus', 1)->count();
        $availableStandsCount = Stand::where('VerhuurdStatus', 0)->count();

        $search = trim($request->input('q', ''));
        $selectedCategory = trim($request->input('category', ''));

        $query = Stand::query()->with('verkoper');

        if ($request->has('empty')) {
            $query->whereNull('Id');
        }

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
            'availableStandsCount'
        ));
    }
}
