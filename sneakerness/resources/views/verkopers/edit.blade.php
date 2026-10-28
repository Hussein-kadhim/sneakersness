{{-- Formulier voor het wijzigen van een bestaande verkoper --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verkoper Wijzigen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Pas de gegevens van de bestaande verkoper aan om de administratie actueel te houden.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/verkopers/verkopersoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-5 sm:py-10">
        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Terug link --}}
            <div class="mb-3 sm:mb-4">
                <a href="{{ route('verkopers.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-orange-600 transition">
                    <span>&larr;</span>
                    <span class="hidden sm:inline">Terug naar verkopersoverzicht</span>
                    <span class="sm:hidden">Terug</span>
                </a>
            </div>

            {{-- Pagina titel en toelichting --}}
            <div class="mb-5 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Verkoper Wijzigen
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    <span class="hidden sm:inline">Pas de gegevens van de bestaande verkoper aan om administratie actueel te houden.</span>
                    <span class="sm:hidden">Pas gegevens van de bestaande verkoper aan.</span>
                </p>
            </div>

            {{-- Algemene foutmelding indien aanwezig --}}
            @if($errors->has('general'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-red-800 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base shrink-0"></i>
                    <span>{{ $errors->first('general') }}</span>
                </div>
            @endif

            {{-- Formulier Kaart --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-8 shadow-sm">
                <form action="{{ route('verkopers.update', $verkoper->Id) }}" method="POST" id="editVerkoperForm" novalidate>
                    @csrf
                    @method('PUT')

                    @php
                        $curDagen = old('dagen', $verkoper->Dagen ?? ($stand?->days_text ?? 'Weekend'));
                        if (in_array($curDagen, ['2', '2 dagen', 'Weekend (Zaterdag & Zondag)'])) {
                            $curDagen = 'Weekend';
                        } elseif (in_array($curDagen, ['1', '1 dag', 'Zaterdag (Eén dag)'])) {
                            $curDagen = 'Zaterdag';
                        } elseif (in_array($curDagen, ['Zondag (Eén dag)'])) {
                            $curDagen = 'Zondag';
                        }

                        $curStatus = old('status', $verkoper->status_label ?? 'Actief');
                        if (empty($curStatus)) {
                            $curStatus = 'Actief';
                        }

                        $curStandType = old('stand_type', $stand?->StandType ?? $verkoper->StandType ?? 'AA+');
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 sm:gap-y-6">

                        {{-- 1. Bedrijfsnaam --}}
                        <div>
                            <label for="naam" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">BEDRIJFSNAAM</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Bedrijfsnaam</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="naam"
                                name="naam"
                                value="{{ old('naam', $verkoper->Naam) }}"
                                placeholder="bijv. Kicks Rotterdam"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('naam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('naam')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 2. Soort Verkoper / Categorie --}}
                        <div>
                            <label for="verkoopt_soort" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">SOORT VERKOPER / CATEGORIE</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Categorie</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="verkoopt_soort"
                                name="verkoopt_soort"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('verkoopt_soort') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                                required
                            >
                                <option value="" disabled {{ old('verkoopt_soort', $verkoper->VerkooptSoort) ? '' : 'selected' }}>Selecteer categorie...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('verkoopt_soort', $verkoper->VerkooptSoort) === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('verkoopt_soort')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 3. Contactpersoon --}}
                        <div>
                            <label for="contactpersoon_naam" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">CONTACTPERSOON</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Contactpersoon</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="contactpersoon_naam"
                                name="contactpersoon_naam"
                                value="{{ old('contactpersoon_naam', $contact?->Naam) }}"
                                placeholder="bijv. Mark de Vries"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('contactpersoon_naam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('contactpersoon_naam')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 4. E-mailadres --}}
                        <div>
                            <label for="contactpersoon_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">E-MAILADRES</span>
                                <span class="sm:hidden normal-case font-bold text-xs">E-mailadres</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="contactpersoon_email"
                                name="contactpersoon_email"
                                value="{{ old('contactpersoon_email', $contact?->Emailadres ?? $contact?->Email) }}"
                                placeholder="bijv. mark@kicksrdam.nl"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('contactpersoon_email') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('contactpersoon_email')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 5. Telefoonnummer --}}
                        <div>
                            <label for="contactpersoon_telefoon" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">TELEFOONNUMMER</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Telefoonnummer</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="contactpersoon_telefoon"
                                name="contactpersoon_telefoon"
                                value="{{ old('contactpersoon_telefoon', $contact?->Telefoonnummer) }}"
                                placeholder="06-12345678"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('contactpersoon_telefoon') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('contactpersoon_telefoon')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 6. Standtype --}}
                        <div>
                            <label for="stand_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">STANDTYPE</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Standtype</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="stand_type"
                                name="stand_type"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('stand_type') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                                required
                            >
                                <option value="" disabled {{ $curStandType ? '' : 'selected' }}>Selecteer standtype...</option>
                                @foreach($standTypes as $code => $label)
                                    <option value="{{ $code }}" {{ $curStandType === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stand_type')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 7. Standnummer / Hal --}}
                        <div>
                            <label for="stand_nummer" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">STANDNUMMER / HAL</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Standnummer / Hal</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="stand_nummer"
                                name="stand_nummer"
                                value="{{ old('stand_nummer', $standNummer) }}"
                                placeholder="Stand #AA-101 • Hal 1"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('stand_nummer') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('stand_nummer')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 8. Aanwezigheidsdagen --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">AANWEZIGHEIDSDAGEN</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Dagen</span>
                                <span class="text-red-500">*</span>
                            </label>

                            {{-- Desktop Select --}}
                            <div class="hidden sm:block">
                                <select
                                    id="dagen_desktop"
                                    name="dagen"
                                    class="w-full px-4 py-3 bg-white border {{ $errors->has('dagen') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                                >
                                    <option value="" disabled {{ $curDagen ? '' : 'selected' }}>Selecteer aanwezigheidsdagen...</option>
                                    <option value="Weekend" {{ $curDagen === 'Weekend' ? 'selected' : '' }}>Weekend (Zaterdag &amp; Zondag)</option>
                                    <option value="Zaterdag" {{ $curDagen === 'Zaterdag' ? 'selected' : '' }}>Zaterdag</option>
                                    <option value="Zondag" {{ $curDagen === 'Zondag' ? 'selected' : '' }}>Zondag</option>
                                </select>
                            </div>

                            {{-- Mobiele Segmented Options (exact zoals mobile screenshot) --}}
                            <div class="sm:hidden grid grid-cols-3 gap-2" id="mobile_dagen_container">
                                <label class="dagen-pill cursor-pointer flex items-center justify-center gap-1.5 py-3 px-2 border rounded-xl text-xs font-semibold text-center transition {{ $curDagen === 'Zaterdag' ? 'border-orange-500 text-orange-600 bg-orange-50/40' : 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50' }}">
                                    <input type="radio" name="dagen_mobile" value="Zaterdag" class="sr-only" {{ $curDagen === 'Zaterdag' ? 'checked' : '' }}>
                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center {{ $curDagen === 'Zaterdag' ? 'border-orange-500' : 'border-slate-300' }}">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 {{ $curDagen === 'Zaterdag' ? '' : 'hidden' }}"></span>
                                    </span>
                                    <span>Zaterdag</span>
                                </label>

                                <label class="dagen-pill cursor-pointer flex items-center justify-center gap-1.5 py-3 px-2 border rounded-xl text-xs font-semibold text-center transition {{ $curDagen === 'Zondag' ? 'border-orange-500 text-orange-600 bg-orange-50/40' : 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50' }}">
                                    <input type="radio" name="dagen_mobile" value="Zondag" class="sr-only" {{ $curDagen === 'Zondag' ? 'checked' : '' }}>
                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center {{ $curDagen === 'Zondag' ? 'border-orange-500' : 'border-slate-300' }}">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 {{ $curDagen === 'Zondag' ? '' : 'hidden' }}"></span>
                                    </span>
                                    <span>Zondag</span>
                                </label>

                                <label class="dagen-pill cursor-pointer flex items-center justify-center gap-1.5 py-3 px-2 border rounded-xl text-xs font-semibold text-center transition {{ $curDagen === 'Weekend' ? 'border-orange-500 text-orange-600 bg-orange-50/40' : 'border-slate-200 text-slate-700 bg-white hover:bg-slate-50' }}">
                                    <input type="radio" name="dagen_mobile" value="Weekend" class="sr-only" {{ $curDagen === 'Weekend' ? 'checked' : '' }}>
                                    <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center {{ $curDagen === 'Weekend' ? 'border-orange-500' : 'border-slate-300' }}">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 {{ $curDagen === 'Weekend' ? '' : 'hidden' }}"></span>
                                    </span>
                                    <span>Weekend</span>
                                </label>
                            </div>

                            @error('dagen')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 9. Status --}}
                        <div>
                            <label for="status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">STATUS</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Status</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('status') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10"
                                required
                            >
                                <option value="Actief" {{ $curStatus === 'Actief' ? 'selected' : '' }}>Actief</option>
                                <option value="Bevestigd" {{ $curStatus === 'Bevestigd' ? 'selected' : '' }}>Bevestigd</option>
                                <option value="In behandeling" {{ $curStatus === 'In behandeling' ? 'selected' : '' }}>In behandeling</option>
                                <option value="Inactief" {{ $curStatus === 'Inactief' ? 'selected' : '' }}>Inactief</option>
                            </select>
                            @error('status')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- 10. Extra Opmerkingen & Faciliteiten --}}
                    <div class="mt-5 sm:mt-6">
                        <label for="opmerking" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                            EXTRA OPMERKINGEN &amp; FACILITEITEN
                        </label>
                        <textarea
                            id="opmerking"
                            name="opmerking"
                            rows="3"
                            placeholder="230V stroompunt vereist, 2 extra kledingrekken, vroege opbouw zaterdagochtend."
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 leading-relaxed"
                        >{{ old('opmerking', $standExtra) }}</textarea>
                        @error('opmerking')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- 11. Bevestigingsmail Checkbox --}}
                    <div class="mt-5 sm:mt-6 flex items-center gap-3">
                        <input
                            type="checkbox"
                            id="stuur_bevestiging"
                            name="stuur_bevestiging"
                            value="1"
                            {{ old('stuur_bevestiging') ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-500 border-slate-300 rounded focus:ring-orange-500 focus:ring-offset-0 cursor-pointer"
                        >
                        <label for="stuur_bevestiging" class="text-xs sm:text-sm text-slate-600 cursor-pointer select-none">
                            <span class="hidden sm:inline">Stuur direct een bevestigingsmail met inloggegevens naar de contactpersoon.</span>
                            <span class="sm:hidden font-medium text-slate-700">Verstuur bevestigingsmail</span>
                        </label>
                    </div>

                    {{-- 12. Knoppen & Footer Acties --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 order-2 sm:order-1 hidden sm:block">
                            * Velden met een rood sterretje zijn verplicht.
                        </p>

                        <div class="flex flex-col-reverse sm:flex-row items-center gap-3 w-full sm:w-auto order-1 sm:order-2 sm:ml-auto">
                            <a
                                href="{{ route('verkopers.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:py-2.5 sm:border sm:border-slate-200 text-slate-700 hover:text-slate-900 sm:hover:bg-slate-50 font-semibold rounded-xl text-sm transition text-center"
                            >
                                Annuleren
                            </a>
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm shadow-sm hover:shadow transition text-center"
                            >
                                Wijzigingen opslaan
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Script om desktop select en mobiele radio buttons synchroon te houden --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var desktopSelect = document.getElementById('dagen_desktop');
            var mobileRadios = document.querySelectorAll('input[name="dagen_mobile"]');
            var pillLabels = document.querySelectorAll('.dagen-pill');

            function updateRadioPills(selectedValue) {
                pillLabels.forEach(function (pill) {
                    var radio = pill.querySelector('input[type="radio"]');
                    var outerDot = pill.querySelector('span.rounded-full');
                    var innerDot = outerDot ? outerDot.querySelector('span') : null;

                    if (radio.value === selectedValue) {
                        radio.checked = true;
                        pill.classList.add('border-orange-500', 'text-orange-600', 'bg-orange-50/40');
                        pill.classList.remove('border-slate-200', 'text-slate-700', 'bg-white');
                        if (outerDot) outerDot.classList.add('border-orange-500');
                        if (innerDot) innerDot.classList.remove('hidden');
                    } else {
                        radio.checked = false;
                        pill.classList.remove('border-orange-500', 'text-orange-600', 'bg-orange-50/40');
                        pill.classList.add('border-slate-200', 'text-slate-700', 'bg-white');
                        if (outerDot) outerDot.classList.remove('border-orange-500');
                        if (innerDot) innerDot.classList.add('hidden');
                    }
                });
            }

            if (desktopSelect) {
                desktopSelect.addEventListener('change', function () {
                    updateRadioPills(this.value);
                });
            }

            mobileRadios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    if (this.checked && desktopSelect) {
                        desktopSelect.value = this.value;
                        updateRadioPills(this.value);
                    }
                });
            });

            // Voorkom dubbele submit
            var form = document.getElementById('editVerkoperForm');
            if (form) {
                form.addEventListener('submit', function () {
                    // Als formulier op mobiel wordt verstuurd en desktop select niet gewijzigd was
                    var checkedRadio = document.querySelector('input[name="dagen_mobile"]:checked');
                    if (checkedRadio && desktopSelect) {
                        desktopSelect.value = checkedRadio.value;
                    }
                });
            }
        });
    </script>

</body>
</html>
