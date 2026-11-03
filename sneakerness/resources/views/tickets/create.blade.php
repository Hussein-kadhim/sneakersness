{{-- Formulier voor het toevoegen van een nieuw ticket (door medewerkers) --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Toevoegen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Registreer een nieuw toegangsticket of verzoek voor Sneakerness Rotterdam.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/tickets/ticketsoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Pagina header --}}
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 mb-2">
                    <a href="{{ route('tickets.index') }}" class="hover:text-orange-600 transition">Tickets Overzicht</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                    <span class="text-slate-900 font-medium">Ticket Toevoegen</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Ticket Toevoegen
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    Registreer een nieuw toegangsticket of bezoekersverzoek voor Sneakerness Rotterdam 2024.
                </p>
            </div>

            {{-- Foutmelding bij database storing (unhappy scenario) --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 sm:p-5 flex items-start gap-3.5 text-red-800 shadow-sm" role="alert">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500 shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-red-900 text-sm sm:text-base">
                            Verbindingsfout
                        </h2>
                        <p class="text-red-700 text-xs sm:text-sm mt-0.5 leading-relaxed">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif

            @if($errors->has('general'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-red-800 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base"></i>
                    <span>{{ $errors->first('general') }}</span>
                </div>
            @endif

            {{-- Formulier Kaart --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <form action="{{ route('tickets.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">

                        {{-- 1. Naam Bezoeker --}}
                        <div class="mb-6">
                            <label for="bezoeker_naam" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Naam Bezoeker <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="bezoeker_naam"
                                name="bezoeker_naam"
                                value="{{ old('bezoeker_naam') }}"
                                placeholder="bijv. Dennis van Dijk"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('bezoeker_naam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('bezoeker_naam')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 2. E-mailadres Bezoeker --}}
                        <div class="mb-6">
                            <label for="bezoeker_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                E-mailadres Bezoeker <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="bezoeker_email"
                                name="bezoeker_email"
                                value="{{ old('bezoeker_email') }}"
                                placeholder="bezoeker@domein.nl"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('bezoeker_email') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('bezoeker_email')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 3. Tickettype / Categorie --}}
                        <div class="mb-6">
                            <label for="ticket_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Tickettype <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="ticket_type"
                                name="ticket_type"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('ticket_type') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                <option value="" disabled {{ old('ticket_type') ? '' : 'selected' }}>Selecteer tickettype...</option>
                                @foreach($ticketTypes as $code => $label)
                                    <option value="{{ $code }}" {{ old('ticket_type', 'Regulier ticket') === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ticket_type')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 4. Evenement --}}
                        <div class="mb-6">
                            <label for="evenement_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Evenement <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="evenement_id"
                                name="evenement_id"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('evenement_id') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                            >
                                @foreach($events as $event)
                                    <option value="{{ $event->Id }}" {{ old('evenement_id') == $event->Id ? 'selected' : '' }}>
                                        {{ $event->Naam }} &bull; {{ $event->Locatie ?? 'Rotterdam' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('evenement_id')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 5. Tijdslot --}}
                        <div class="mb-6">
                            <label for="tijdslot" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Tijdslot &amp; Datum <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="tijdslot"
                                name="tijdslot"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('tijdslot') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                @foreach($timeSlots as $slotVal => $slotLabel)
                                    <option value="{{ $slotVal }}" {{ old('tijdslot', 'Zaterdag 16 nov (10:00 - 18:00)') === $slotVal ? 'selected' : '' }}>
                                        {{ $slotLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tijdslot')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 6. Zaal / Toegang --}}
                        <div class="mb-6">
                            <label for="zaal_toegang" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Zaal / Toegang <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="zaal_toegang"
                                name="zaal_toegang"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('zaal_toegang') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                @foreach($zaalOpties as $zaalVal => $zaalLabel)
                                    <option value="{{ $zaalVal }}" {{ old('zaal_toegang', 'Hal 1 • Entree A') === $zaalVal ? 'selected' : '' }}>
                                        {{ $zaalLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zaal_toegang')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 7. Aantal Tickets --}}
                        <div class="mb-6">
                            <label for="aantal_tickets" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Aantal Tickets <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="aantal_tickets"
                                name="aantal_tickets"
                                min="1"
                                max="10"
                                value="{{ old('aantal_tickets', 1) }}"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('aantal_tickets') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('aantal_tickets')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 8. Status --}}
                        <div class="mb-6">
                            <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('status') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                @foreach($statussen as $statVal => $statLabel)
                                    <option value="{{ $statVal }}" {{ old('status', 'Geldig') === $statVal ? 'selected' : '' }}>
                                        {{ $statLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- 9. Extra Opmerkingen --}}
                    <div class="mt-6">
                        <label for="opmerking" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Extra Opmerkingen &amp; Details
                        </label>
                        <textarea
                            id="opmerking"
                            name="opmerking"
                            rows="3"
                            placeholder="Bijv. Vroege scan vereist, VIP polsbandje afhalen bij balie..."
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >{{ old('opmerking') }}</textarea>
                        @error('opmerking')
                            <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Footer acties --}}
                    <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400">
                            * Velden met een rood sterretje zijn verplicht.
                        </p>
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <a
                                href="{{ route('tickets.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold rounded-xl text-xs sm:text-sm transition"
                            >
                                Annuleren
                            </a>
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-xs sm:text-sm shadow-sm transition"
                            >
                                Opslaan &amp; Ticket toevoegen
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

