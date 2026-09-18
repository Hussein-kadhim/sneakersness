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

        $query = Stand::query()->with('verkoper');

        $stands = $query->orderBy('Id', 'asc')->paginate(6)->withQueryString();

        return view('stands.index', compact(
            'stands',
            'totalStands',
            'countAAPlus',
            'countAA',
            'countA',
            'rentedStandsCount',
            'availableStandsCount'
        ));
    }
}
