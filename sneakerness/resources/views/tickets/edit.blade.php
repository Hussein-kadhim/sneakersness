{{-- Formulier voor het wijzigen van een bestaand ticket --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Wijzigen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Pas de gegevens van het ticket aan om de administratie en toegang actueel te houden.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/tickets/ticketsoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-5 sm:py-10">
        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Terug link --}}
            <div class="mb-3 sm:mb-4">
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-slate-500 hover:text-orange-600 transition">
                    <span>&larr;</span>
                    <span class="hidden sm:inline">Terug naar ticketsoverzicht</span>
                    <span class="sm:hidden">Terug</span>
                </a>
            </div>

            {{-- Pagina titel en toelichting --}}
            <div class="mb-5 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Ticket Wijzigen
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    <span class="hidden sm:inline">Pas de gegevens van uw ticket aan om de registratie en toegang actueel te houden.</span>
                    <span class="sm:hidden">Pas de gegevens van uw ticket aan.</span>
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
                        <p class="text-red-700 text-xs sm:text-sm mt-0.5 leading-relaxed font-medium">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif

            @if($errors->has('general'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-red-800 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base shrink-0"></i>
                    <span>{{ $errors->first('general') }}</span>
                </div>
            @endif

            {{-- Ticket samenvatting balk --}}
            <div class="mb-6 bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">GESELECTEERD TICKET</span>
                    <h2 class="text-lg font-bold text-slate-900 mt-0.5">
                        {{ $ticket->ticket_code_display }} &bull; {{ $ticket->bestelnummer_display }}
                    </h2>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $ticket->status_color_class }}">
                        {{ $ticket->status_label }}
                    </span>
                </div>
            </div>

            {{-- Formulier Kaart --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-8 shadow-sm">
                <form action="{{ route('tickets.update', $ticket->Id) }}" method="POST" id="editTicketForm" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 sm:gap-y-6">

                        {{-- 1. Naam Bezoeker --}}
                        <div>
                            <label for="bezoeker_naam" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">NAAM BEZOEKER</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Naam bezoeker</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="bezoeker_naam"
                                name="bezoeker_naam"
                                value="{{ old('bezoeker_naam', $ticket->bezoeker?->Naam) }}"
                                placeholder="bijv. Dennis van Dijk"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('bezoeker_naam') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('bezoeker_naam')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 2. E-mailadres Bezoeker --}}
                        <div>
                            <label for="bezoeker_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">E-MAILADRES BEZOEKER</span>
                                <span class="sm:hidden normal-case font-bold text-xs">E-mailadres</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="bezoeker_email"
                                name="bezoeker_email"
                                value="{{ old('bezoeker_email', $ticket->bezoeker?->Emailadres ?? $ticket->bezoeker?->Email) }}"
                                placeholder="bijv. dennis@domein.nl"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('bezoeker_email') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('bezoeker_email')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 3. Tickettype --}}
                        <div>
                            <label for="ticket_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">TICKETTYPE</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Tickettype</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="ticket_type"
                                name="ticket_type"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('ticket_type') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                @foreach($ticketTypes as $code => $label)
                                    <option value="{{ $code }}" {{ old('ticket_type', $ticket->TicketType) === $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ticket_type')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 4. Evenement --}}
                        <div>
                            <label for="evenement_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">EVENEMENT</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Evenement</span>
                            </label>
                            <select
                                id="evenement_id"
                                name="evenement_id"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('evenement_id') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                            >
                                @foreach($events as $event)
                                    <option value="{{ $event->Id }}" {{ old('evenement_id', $ticket->EvenementId) == $event->Id ? 'selected' : '' }}>
                                        {{ $event->Naam }} &bull; {{ $event->Locatie ?? 'Rotterdam' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('evenement_id')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 5. Tijdslot --}}
                        <div>
                            <label for="tijdslot" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">TIJDSLOT &amp; DATUM</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Tijdslot</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="tijdslot"
                                name="tijdslot"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('tijdslot') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                @foreach($timeSlots as $slotVal => $slotLabel)
                                    <option value="{{ $slotVal }}" {{ old('tijdslot', $ticket->Tijdslot) === $slotVal ? 'selected' : '' }}>
                                        {{ $slotLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tijdslot')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 6. Zaal / Toegang --}}
                        <div>
                            <label for="zaal_toegang" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">ZAAL / TOEGANG</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Zaal / Toegang</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="zaal_toegang"
                                name="zaal_toegang"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('zaal_toegang') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                @foreach($zaalOpties as $zaalVal => $zaalLabel)
                                    <option value="{{ $zaalVal }}" {{ old('zaal_toegang', $ticket->ZaalToegang) === $zaalVal ? 'selected' : '' }}>
                                        {{ $zaalLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zaal_toegang')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 7. Aantal Tickets --}}
                        <div>
                            <label for="aantal_tickets" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">AANTAL TICKETS</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Aantal</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="aantal_tickets"
                                name="aantal_tickets"
                                min="1"
                                max="10"
                                value="{{ old('aantal_tickets', $ticket->AantalTickets ?? 1) }}"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('aantal_tickets') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                            @error('aantal_tickets')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- 8. Status --}}
                        <div>
                            <label for="status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <span class="hidden sm:inline">STATUS</span>
                                <span class="sm:hidden normal-case font-bold text-xs">Status</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="w-full px-4 py-3 bg-white border {{ $errors->has('status') ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-slate-200 focus:ring-orange-500 focus:border-orange-500' }} rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2"
                                required
                            >
                                @foreach($statussen as $statVal => $statLabel)
                                    <option value="{{ $statVal }}" {{ old('status', $ticket->status_label) === $statVal ? 'selected' : '' }}>
                                        {{ $statLabel }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- 9. Extra Opmerkingen --}}
                    <div class="mt-5 sm:mt-6">
                        <label for="opmerking" class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-2">
                            EXTRA OPMERKINGEN &amp; FACILITEITEN
                        </label>
                        <textarea
                            id="opmerking"
                            name="opmerking"
                            rows="3"
                            placeholder="Bijv. Vroege scan vereist, VIP polsbandje afhalen bij balie..."
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 leading-relaxed"
                        >{{ old('opmerking', $ticket->Opmerking) }}</textarea>
                        @error('opmerking')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1 font-medium">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Knoppen & Footer Acties --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-400 order-2 sm:order-1 hidden sm:block">
                            * Velden met een rood sterretje zijn verplicht.
                        </p>

                        <div class="flex flex-col-reverse sm:flex-row items-center gap-3 w-full sm:w-auto order-1 sm:order-2 sm:ml-auto">
                            <a
                                href="{{ route('tickets.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:py-2.5 sm:border sm:border-slate-200 text-slate-700 hover:text-slate-900 sm:hover:bg-slate-50 font-semibold rounded-xl text-sm transition text-center"
                            >
                                Annuleren
                            </a>
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm shadow-sm hover:shadow transition text-center cursor-pointer"
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

</body>
</html>

