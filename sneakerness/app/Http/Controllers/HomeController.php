<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use App\Models\Verkoper;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $verkopers = Verkoper::query()
            ->with('stands')
            ->where('IsActief', 1)
            ->orderByDesc('SpecialeStatus')
            ->orderBy('Naam')
            ->limit(6)
            ->get();

        $totalStands = Stand::count();

        return view('home.index', [
            'verkopers' => $verkopers,
            'totalVerkopers' => Verkoper::count(),
            'partnerCount' => Verkoper::where('SpecialeStatus', 1)->count(),
            'rentedStandsCount' => Stand::where('VerhuurdStatus', 1)->count(),
            'totalStands' => $totalStands > 0 ? $totalStands : 55,
            'categories' => $this->categories(),
        ]);
    }

    private function categories(): Collection
    {
        return Verkoper::query()
            ->whereNotNull('VerkooptSoort')
            ->where('VerkooptSoort', '!=', '')
            ->selectRaw('VerkooptSoort as name, COUNT(*) as total')
            ->groupBy('VerkooptSoort')
            ->orderByDesc('total')
            ->limit(4)
            ->get();
    }
}