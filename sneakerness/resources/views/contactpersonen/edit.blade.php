<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contactpersoon wijzigen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Pas de gegevens van de contactpersoon aan voor Sneakerness Rotterdam.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/contactpersonen/contactpersonenoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900 font-['Plus_Jakarta_Sans',sans-serif]">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-[860px] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Terug link --}}
            <div class="mb-3">
                <a href="{{ route('contactpersonen.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Terug naar contactpersonen</span>
                </a>
            </div>

            {{-- Pagina titel en toelichting --}}
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Contactpersoon wijzigen
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    <span class="sm:hidden">Pas de contactgegevens of standkoppeling aan.</span>
                    <span class="hidden sm:inline">Pas de persoonsgegevens, contactinformatie of de koppeling aan een verkoper aan.</span>
                </p>
            </div>

            {{-- Foutmeldingen bovenaan --}}
            @if($errors->has('general'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-red-800 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                    <span>{{ $errors->first('general') }}</span>
                </div>
            @endif

            {{-- Succesmelding banner indien van toepassing --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3 text-emerald-800 text-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Formulier Kaart --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <form action="{{ route('contactpersonen.update', $contactpersoon->Id) }}" method="POST" novalidate>
                    @csrf
                    @method('PUT')

                    @php
                        $gekoppeldeVerkoper = $contactpersoon->verkopers->first();
                        $geselecteerdeVerkoperId = old('verkoper_id', $gekoppeldeVerkoper?->Id ?? '');
                        $isPrimair = old('is_primair', $gekoppeldeVerkoper ? str_contains($gekoppeldeVerkoper->pivot->Opmerking ?? '', 'Hoofdcontactpersoon') : false);
                        $huidigeRol = old('functie', $contactpersoon->Opmerking ?: 'Standmanager');
                        $huidigeStatus = old('status', ($contactpersoon->status_label === 'In behandeling' || !$contactpersoon->IsActief) ? 'In behandeling' : 'Actief');
                    @endphp

                    {{-- 1. Persoonlijke gegevens --}}
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fa-regular fa-user text-slate-400 text-sm sm:hidden"></i>
                            <span>Persoonlijke gegevens</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-4">
                            {{-- Voornaam --}}
                            <div>
                                <label for="voornaam" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    Voornaam <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="voornaam"
                                    name="voornaam"
                                    value="{{ old('voornaam', $contactpersoon->voornaam) }}"
                                    placeholder="Mark"
                                    class="w-full px-4 py-3 bg-white border {{ $errors->has('voornaam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                >
                                @error('voornaam')
                                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            {{-- Achternaam --}}
                            <div>
                                <label for="achternaam" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    Achternaam <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="achternaam"
                                    name="achternaam"
                                    value="{{ old('achternaam', $contactpersoon->achternaam) }}"
                                    placeholder="de Vries"
                                    class="w-full px-4 py-3 bg-white border {{ $errors->has('achternaam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                >
                                @error('achternaam')
                                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Functie / Rol --}}
                        <div>
                            <label for="functie" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Functie / Rol <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select
                                    id="functie"
                                    name="functie"
                                    class="w-full appearance-none px-4 py-3 bg-white border {{ $errors->has('functie') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 pr-10 cursor-pointer"
                                >
                                    @php
                                        $rollen = ['Standmanager', 'Eigenaar', 'Sales', 'Customizer', 'Barbier', 'Vertegenwoordiger'];
                                        if ($huidigeRol && !in_array($huidigeRol, $rollen)) {
                                            $rollen[] = $huidigeRol;
                                        }
                                    @endphp
                                    @foreach($rollen as $rol)
                                        <option value="{{ $rol }}" {{ $huidigeRol === $rol ? 'selected' : '' }}>
                                            {{ $rol }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('functie')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. Contactgegevens --}}
                    <div class="pt-6 mt-6 border-t border-slate-100">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-at text-slate-400 text-sm sm:hidden"></i>
                            <span>Contactgegevens</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            {{-- E-mailadres --}}
                            <div>
                                <label for="emailadres" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    E-mailadres <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="emailadres"
                                    name="emailadres"
                                    value="{{ old('emailadres', $contactpersoon->Emailadres) }}"
                                    placeholder="mark@kicksrdam.nl"
                                    class="w-full px-4 py-3 bg-white border {{ $errors->has('emailadres') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                >
                                @error('emailadres')
                                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>

                            {{-- Telefoonnummer --}}
                            <div>
                                <label for="telefoonnummer" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    Telefoonnummer <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="telefoonnummer"
                                    name="telefoonnummer"
                                    value="{{ old('telefoonnummer', $contactpersoon->Telefoonnummer) }}"
                                    placeholder="06-12345678"
                                    class="w-full px-4 py-3 bg-white border {{ $errors->has('telefoonnummer') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                >
                                @error('telefoonnummer')
                                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 3. Koppeling aan Verkoper / Stand --}}
                    <div class="pt-6 mt-6 border-t border-slate-100">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-store text-slate-400 text-sm sm:hidden"></i>
                            <span class="hidden sm:inline">Koppeling aan Verkoper / Stand</span>
                            <span class="sm:hidden">Koppelen aan verkoper</span>
                        </h2>

                        <div class="mb-4">
                            <label for="verkoper_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">Koppel aan verkoper / stand</span>
                                <span class="sm:hidden">Verkoper</span>
                            </label>
                            <div class="relative">
                                <select
                                    id="verkoper_id"
                                    name="verkoper_id"
                                    class="w-full appearance-none px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pr-10 cursor-pointer"
                                >
                                    <option value="">Geen verkoper gekoppeld</option>
                                    @foreach($verkopers as $verkoper)
                                        @php
                                            $stand = $verkoper->stands->first();
                                            $standTekst = $stand ? ' (' . $stand->location_display . ')' : '';
                                            $isGeselecteerd = (string)$geselecteerdeVerkoperId === (string)$verkoper->Id;
                                        @endphp
                                        <option value="{{ $verkoper->Id }}" {{ $isGeselecteerd ? 'selected' : '' }}>
                                            {{ $verkoper->Naam }}{{ $standTekst }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Primair aanspreekpunt checkbox --}}
                        {{-- Desktop versie --}}
                        <div class="hidden sm:flex items-center gap-2.5">
                            <input
                                type="checkbox"
                                id="is_primair_desktop"
                                name="is_primair"
                                value="1"
                                {{ $isPrimair ? 'checked' : '' }}
                                class="w-4 h-4 text-orange-500 border-slate-300 rounded focus:ring-orange-500 cursor-pointer accent-orange-500"
                            >
                            <label for="is_primair_desktop" class="text-sm font-medium text-slate-800 cursor-pointer select-none">
                                Instellen als primair aanspreekpunt voor deze exposant
                            </label>
                        </div>

                        {{-- Mobiele weergave kaartje --}}
                        <div class="sm:hidden bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-start gap-3">
                            <input
                                type="checkbox"
                                id="is_primair_mobile"
                                name="is_primair"
                                value="1"
                                {{ $isPrimair ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-orange-500 border-slate-300 rounded focus:ring-orange-500 cursor-pointer accent-orange-500"
                            >
                            <label for="is_primair_mobile" class="cursor-pointer select-none">
                                <span class="block text-sm font-bold text-slate-900">Primair aanspreekpunt</span>
                                <span class="block text-xs text-slate-500 mt-0.5 leading-snug">
                                    Dit contact wordt als eerste benaderd voor operationele zaken rondom de stand.
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- 4. Status --}}
                    <div class="pt-6 mt-6 border-t border-slate-100">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-toggle-on text-slate-400 text-sm sm:hidden"></i>
                            <span>Status</span>
                        </h2>

                        {{-- Desktop radio buttons --}}
                        <div class="hidden sm:flex items-center gap-6">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input
                                    type="radio"
                                    name="status"
                                    value="Actief"
                                    {{ $huidigeStatus === 'Actief' ? 'checked' : '' }}
                                    class="w-4 h-4 text-orange-500 border-slate-300 focus:ring-orange-500 accent-orange-500"
                                >
                                <span class="text-sm font-medium text-slate-900">Actief</span>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input
                                    type="radio"
                                    name="status"
                                    value="In behandeling"
                                    {{ $huidigeStatus === 'In behandeling' ? 'checked' : '' }}
                                    class="w-4 h-4 text-orange-500 border-slate-300 focus:ring-orange-500 accent-orange-500"
                                >
                                <span class="text-sm font-medium text-slate-900">In behandeling</span>
                            </label>
                        </div>

                        {{-- Mobiele dropdown select --}}
                        <div class="sm:hidden">
                            <label for="status_select" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Status van contactpersoon
                            </label>
                            <div class="relative">
                                <select
                                    id="status_select"
                                    onchange="document.querySelectorAll('input[name=status]').forEach(r => { if(r.value === this.value) r.checked = true; });"
                                    class="w-full appearance-none px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pr-10 cursor-pointer"
                                >
                                    <option value="Actief" {{ $huidigeStatus === 'Actief' ? 'selected' : '' }}>Actief</option>
                                    <option value="In behandeling" {{ $huidigeStatus === 'In behandeling' ? 'selected' : '' }}>In behandeling</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Knoppen --}}
                    {{-- Desktop weergave --}}
                    <div class="hidden sm:flex items-center justify-end gap-3 pt-6 mt-8 border-t border-slate-100">
                        <a href="{{ route('contactpersonen.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition">
                            Annuleren
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                            Wijzigingen Opslaan
                        </button>
                    </div>

                    {{-- Mobiele weergave --}}
                    <div class="sm:hidden flex flex-col gap-2.5 pt-6 mt-6 border-t border-slate-100">
                        <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i>
                            <span>Wijzigingen Opslaan</span>
                        </button>
                        <a href="{{ route('contactpersonen.index') }}" class="w-full py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition text-center">
                            Annuleren
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </main>

    {{-- Algemene voettekst --}}
    @include('partials.footer')

    <script>
        // Sync mobile checkbox and status dropdown with form inputs
        document.addEventListener('DOMContentLoaded', function() {
            var mobileCb = document.getElementById('is_primair_mobile');
            var desktopCb = document.getElementById('is_primair_desktop');
            if (mobileCb && desktopCb) {
                mobileCb.addEventListener('change', function() {
                    desktopCb.checked = this.checked;
                });
                desktopCb.addEventListener('change', function() {
                    mobileCb.checked = this.checked;
                });
            }
        });
    </script>
</body>
</html>
