<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

// Controller voor het beheren van het gebruikersprofiel en accountinstellingen
class ProfileController extends Controller
{
    // Toont het formulier om het profiel aan te passen
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Werkt de persoonsgegevens (naam, e-mail) van de ingelogde gebruiker bij
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // E-mail verificatie resetten als het e-mailadres is gewijzigd
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Verwijdert het gebruikersaccount na controle van het huidige wachtwoord
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Uitloggen en account verwijderen
        Auth::logout();

        $user->delete();

        // Sessie ongeldig maken en CSRF token vernieuwen
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
