<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Inloggen bij Sneakerness
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Vul je inloggegevens in om toegang te krijgen tot het portaal.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('E-mailadres')" class="text-xs font-bold text-slate-700 uppercase tracking-wider" />
            <x-text-input
                id="email"
                class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm"
                type="email"
                name="email"
                :value="old('email')"
                placeholder="naam@sneakerness.com"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Wachtwoord')" class="text-xs font-bold text-slate-700 uppercase tracking-wider" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-orange-600 hover:text-orange-700 hover:underline" href="{{ route('password.request') }}">
                        Wachtwoord vergeten?
                    </a>
                @endif
            </div>

            <x-text-input
                id="password"
                class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm"
                type="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-orange-500 focus:ring-orange-500" name="remember">
                <span class="ms-2 text-xs text-slate-600 font-medium">Onthoud mij</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Inloggen
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-600">
            Nog geen account?
            <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700 hover:underline">
                Registreren
            </a>
        </p>
    </div>
</x-guest-layout>
