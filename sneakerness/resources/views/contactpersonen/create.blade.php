<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contactpersoon Toevoegen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Voer de gegevens in van de nieuwe contactpersoon voor Sneakerness Rotterdam.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/contactpersonen/contactpersonenoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Pagina header --}}
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-2">
                    <a href="{{ route('contactpersonen.index') }}" class="hover:text-orange-600 transition">Contactpersonen Overzicht</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                    <span class="text-slate-900 font-medium">Contactpersoon Toevoegen</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Contactpersoon Toevoegen
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    Voer de gegevens in van de nieuwe contactpersoon voor Sneakerness Rotterdam.
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
                <form action="{{ route('contactpersonen.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        
                        {{-- 1. Voornaam --}}
                        <div class="mb-2">
                            <label for="voornaam" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Voornaam <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="voornaam"
                                name="voornaam"
                                value="{{ old('voornaam') }}"
                                placeholder="bijv. Dennis"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('voornaam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('voornaam')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 2. Achternaam --}}
                        <div class="mb-2">
                            <label for="achternaam" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Achternaam <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="achternaam"
                                name="achternaam"
                                value="{{ old('achternaam') }}"
                                placeholder="bijv. de Ridder"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('achternaam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('achternaam')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 3. E-mailadres --}}
                        <div class="mb-2">
                            <label for="emailadres" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                E-mailadres <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="emailadres"
                                name="emailadres"
                                value="{{ old('emailadres') }}"
                                placeholder="dennis@example.nl"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('emailadres') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('emailadres')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 4. Telefoonnummer --}}
                        <div class="mb-2">
                            <label for="telefoonnummer" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Telefoonnummer <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="telefoonnummer"
                                name="telefoonnummer"
                                value="{{ old('telefoonnummer') }}"
                                placeholder="06-12345678"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('telefoonnummer') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                            >
                            @error('telefoonnummer')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- 5. Extra Opmerkingen & Rol --}}
                    <div class="mt-6">
                        <label for="opmerking" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Functie / Opmerking
                        </label>
                        <textarea
                            id="opmerking"
                            name="opmerking"
                            rows="3"
                            placeholder="Bijv. Standmanager, Eigenaar, Sales..."
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >{{ old('opmerking') }}</textarea>
                        @error('opmerking')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Footer acties --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400">
                            * Velden met een rood sterretje zijn verplicht.
                        </p>
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <a
                                href="{{ route('contactpersonen.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold rounded-xl text-xs sm:text-sm transition"
                            >
                                Annuleren
                            </a>
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-xs sm:text-sm shadow-sm transition"
                            >
                                Opslaan
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
