<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Account aanmaken
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Meld je aan voor Sneakerness Rotterdam.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Volledige naam')" class="text-xs font-bold text-slate-700 uppercase tracking-wider" />
            <x-text-input
                id="name"
                class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm"
                type="text"
                name="name"
                :value="old('name')"
                placeholder="Jan de Vries"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

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
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="role" :value="__('Selecteer je rol')" class="text-xs font-bold text-slate-700 uppercase tracking-wider" />
            <select
                id="role"
                name="role"
                class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm text-slate-800"
                required
            >
                <option value="bezoeker" {{ old('role') == 'bezoeker' ? 'selected' : '' }}>
                    Bezoeker (Tickets &amp; Shoppen)
                </option>
                <option value="verkoper" {{ old('role') == 'verkoper' ? 'selected' : '' }}>
                    Verkoper (Sneakers- of standhouder)
                </option>
                <option value="organisator" {{ old('role') == 'organisator' ? 'selected' : '' }}>
                    Organisator (Evenementbeheerder)
                </option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Wachtwoord')" class="text-xs font-bold text-slate-700 uppercase tracking-wider" />
            <x-text-input
                id="password"
                class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm"
                type="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Wachtwoord bevestigen')" class="text-xs font-bold text-slate-700 uppercase tracking-wider" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm"
                type="password"
                name="password_confirmation"
                placeholder="••••••••"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Registreren
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-600">
            Al een account?
            <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-700 hover:underline">
                Inloggen
            </a>
        </p>
    </div>
</x-guest-layout>
