<?php

namespace App\Http\Controllers;

use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Stand::query()->with('verkoper');

        $stands = $query->orderBy('Id', 'asc')->paginate(6)->withQueryString();

        return view('stands.index', compact('stands'));
    }
}
