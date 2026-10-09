<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// Controller voor inloggen en uitloggen van gebruikers
class AuthenticatedSessionController extends Controller
{
    // Toont het inlogscherm
    public function create(): View
    {
        return view('auth.login');
    }

    // Verwerkt het inlogverzoek en vernieuwt de sessie-ID
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('verkopers.index', absolute: false));
    }

    // Beëindigt de sessie en logt de gebruiker uit
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
