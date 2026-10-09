{{-- Formulier voor het toevoegen van een nieuwe verkoper --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verkoper Toevoegen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Voer de gegevens in van de nieuwe deelnemende verkoper voor Sneakerness Rotterdam.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/verkopers/verkopersoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Pagina header --}}
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-2">
                    <a href="{{ route('verkopers.index') }}" class="hover:text-orange-600 transition">Verkopers Overzicht</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                    <span class="text-slate-900 font-medium">Verkoper Toevoegen</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Verkoper Toevoegen
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    Voer de gegevens in van de nieuwe deelnemende verkoper voor Sneakerness Rotterdam 2024.
                </p>
            </div>

            {{-- Algemene foutmelding indien van toepassing --}}
            @if($errors->has('general'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-red-800 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base"></i>
                    <span>{{ $errors->first('general') }}</span>
                </div>
            @endif

            {{-- Formulier Kaart --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <form action="{{ route('verkopers.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        
                        {{-- 1. Bedrijfsnaam --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="naam" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Bedrijfsnaam <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="naam"
                                name="naam"
                                value="{{ old('naam') }}"
                                placeholder="bijv. Solebox Rotterdam"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('naam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('naam')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 2. Soort Verkoper / Categorie --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="verkoopt_soort" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Soort Verkoper / Categorie <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="verkoopt_soort"
                                name="verkoopt_soort"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('verkoopt_soort') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                            >
                                <option value="" disabled {{ old('verkoopt_soort') ? '' : 'selected' }}>Selecteer categorie...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('verkoopt_soort') === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('verkoopt_soort')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 3. Contactpersoon --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="contactpersoon_naam" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Contactpersoon <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="contactpersoon_naam"
                                name="contactpersoon_naam"
                                value="{{ old('contactpersoon_naam') }}"
                                placeholder="Volledige naam"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('contactpersoon_naam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('contactpersoon_naam')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 4. E-mailadres --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="contactpersoon_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                E-mailadres <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="contactpersoon_email"
                                name="contactpersoon_email"
                                value="{{ old('contactpersoon_email') }}"
                                placeholder="verkoper@domein.nl"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('contactpersoon_email') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('contactpersoon_email')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 5. Telefoonnummer --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="contactpersoon_telefoon" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Telefoonnummer <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="contactpersoon_telefoon"
                                name="contactpersoon_telefoon"
                                value="{{ old('contactpersoon_telefoon') }}"
                                placeholder="+31 6 12345678"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('contactpersoon_telefoon') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('contactpersoon_telefoon')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 6. Standtype --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="stand_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Standtype <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="stand_type"
                                name="stand_type"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('stand_type') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                            >
                                <option value="" disabled {{ old('stand_type') ? '' : 'selected' }}>Selecteer standtype...</option>
                                @foreach($standTypes as $code => $label)
                                    <option value="{{ $code }}" {{ old('stand_type') === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stand_type')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 7. Standnummer / Hal --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="stand_nummer" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Standnummer / Hal <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="stand_nummer"
                                name="stand_nummer"
                                value="{{ old('stand_nummer') }}"
                                placeholder="bijv. Stand #AA-105 • Hal 1"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('stand_nummer') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('stand_nummer')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 8. Aanwezigheidsdagen --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="dagen" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Aanwezigheidsdagen <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="dagen"
                                name="dagen"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('dagen') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                            >
                                <option value="" disabled {{ old('dagen') ? '' : 'selected' }}>Selecteer dagen...</option>
                                <option value="Weekend" {{ old('dagen', 'Weekend') === 'Weekend' ? 'selected' : '' }}>Weekend (Beide dagen)</option>
                                <option value="Zaterdag" {{ old('dagen') === 'Zaterdag' ? 'selected' : '' }}>Zaterdag (Eén dag)</option>
                                <option value="Zondag" {{ old('dagen') === 'Zondag' ? 'selected' : '' }}>Zondag (Eén dag)</option>
                            </select>
                            @error('dagen')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 9. Status --}}
                        <div class="mb-6" style="margin-bottom: 1.5rem;">
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2" style="margin-bottom: 0.5rem;">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('status') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                            >
                                <option value="Actief" {{ old('status', 'Actief') === 'Actief' ? 'selected' : '' }}>Actief</option>
                                <option value="In behandeling" {{ old('status') === 'In behandeling' ? 'selected' : '' }}>In behandeling</option>
                                <option value="Inactief" {{ old('status') === 'Inactief' ? 'selected' : '' }}>Inactief</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- 10. Extra Opmerkingen & Faciliteiten --}}
                    <div class="mt-8">
                        <label for="opmerking" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Extra Opmerkingen &amp; Faciliteiten
                        </label>
                        <textarea
                            id="opmerking"
                            name="opmerking"
                            rows="3"
                            placeholder="Bijv. 230V stroompunt vereist, extra kledingrekken, vroege opbouw..."
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >{{ old('opmerking') }}</textarea>
                        @error('opmerking')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- 11. Bevestigingsmail Checkbox --}}
                    <div class="mt-6 flex items-center gap-3">
                        <input
                            type="checkbox"
                            id="stuur_bevestiging"
                            name="stuur_bevestiging"
                            value="1"
                            {{ old('stuur_bevestiging') ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-600 border-slate-300 rounded focus:ring-orange-500"
                        >
                        <label for="stuur_bevestiging" class="text-xs sm:text-sm text-slate-600">
                            Stuur direct een bevestigingsmail met inloggegevens naar de contactpersoon.
                        </label>
                    </div>

                    {{-- Footer acties --}}
                    <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400">
                            * Velden met een rood sterretje zijn verplicht.
                        </p>
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <a
                                href="{{ route('verkopers.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold rounded-xl text-xs sm:text-sm transition"
                            >
                                Annuleren
                            </a>
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-xs sm:text-sm shadow-sm transition"
                            >
                                Opslaan &amp; Verkoper toevoegen
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </main>

    {{-- Footer --}}
    @include('partials.footer')

</body>
</html>
