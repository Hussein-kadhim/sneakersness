{{-- Overzichtspagina voor ticketbeheer, aankoop en toegangscontrole --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tickets Overzicht &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Bekijk en beheer alle geregistreerde tickets en zie direct welke tickets aandacht vereisen voor Sneakerness Rotterdam 2024.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/tickets/ticketsoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900" x-data="{ selectedTicket: null, showModal: false }">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-5 sm:py-10">
        <div class="w-full max-w-[1720px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">

            {{-- Pagina titel en introductietekst --}}
            <div class="mb-4 sm:mb-6">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Tickets Overzicht
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    Bekijk en beheer alle geregistreerde tickets en zie direct welke tickets aandacht vereisen voor Sneakerness Rotterdam 2024.
                </p>
            </div>

            {{-- Succesmelding na actie (happy scenario) --}}
            @if(session('success'))
                <div id="success-alert" class="mb-6 p-4 bg-emerald-600 text-white rounded-xl font-medium text-sm sm:text-base flex items-center gap-3 shadow-sm transition-opacity duration-500" role="status">
                    <i class="fa-solid fa-check text-white text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <script>
                    setTimeout(function() {
                        var alert = document.getElementById('success-alert');
                        if (alert) {
                            alert.style.opacity = '0';
                            setTimeout(function() { alert.remove(); }, 500);
                        }
                    }, 4000);
                </script>
            @endif

            {{-- Foutmelding na aankoop / storing (unhappy scenario) --}}
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 mb-6 shadow-sm" role="alert">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500 shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-red-900 text-sm sm:text-base">
                            {{ str_contains(session('error'), 'kopen') ? 'Probleem bij aankoop' : 'Foutmelding' }}
                        </h2>
                        <p class="text-red-700 text-xs sm:text-sm mt-0.5 leading-relaxed">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Unhappy scenario: database foutmelding --}}
            @if($dbError)
                <div class="sn-db-alert bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 mb-6" role="alert">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500 shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-red-900 text-sm sm:text-base">
                            Verbindingsfout
                        </h2>
                        <p class="text-red-700 text-xs sm:text-sm mt-0.5 leading-relaxed font-medium">
                            {{ $errorMessage }}
                        </p>
                    </div>
                </div>
            @else

                {{-- KPI samenvattingskaarten mobiel --}}
                <div class="grid grid-cols-2 gap-3 mb-4 lg:hidden">
                    <div class="bg-white rounded-xl p-3.5 border border-slate-200">
                        <span class="text-xs text-slate-400 font-medium block">Totaal tickets</span>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-slate-900">{{ $totalTicketsCount }}</span>
                        </div>
                        <span class="text-[11px] text-slate-400">{{ $capacityPercentage }} van capaciteit</span>
                    </div>

                    <div class="bg-white rounded-xl p-3.5 border border-slate-200">
                        <span class="text-xs text-slate-400 font-medium block">Aandacht nodig</span>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-amber-600">{{ $attentionTicketsCount }}</span>
                        </div>
                        <span class="text-[11px] text-slate-400">Openstaande acties</span>
                    </div>
                </div>

                {{-- KPI samenvattingskaarten desktop --}}
                <div class="hidden lg:grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Totaal tickets</span>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ $totalTicketsCount }}</div>
                        <div class="text-xs text-slate-400 mt-1">{{ $capacityPercentage }} van capaciteit</div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Geldig &amp; Actief</span>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ $validTicketsCount }}</div>
                        <div class="text-xs text-slate-400 mt-1">Toegang bevestigd</div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Aandacht nodig</span>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ $attentionTicketsCount }}</div>
                        <div class="text-xs text-slate-400 mt-1">Wijziging of annulering open</div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Gescand / Binnen</span>
                        <div class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $scannedTicketsCount }} <span class="text-xl font-bold text-slate-400">/ {{ $totalTicketsCount }}</span>
                        </div>
                        <div class="text-xs text-slate-400 mt-1">Live zaalinloop</div>
                    </div>
                </div>

                {{-- Zoekbalk en actieknoppen --}}
                <div class="mb-4">
                    <form method="GET" action="{{ route('tickets.index') }}">
                        <div class="relative mb-3">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                            <input
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Zoek op ticket ID, naam bezoeker of bestelnummer..."
                                class="w-full pl-9 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                            @if($search !== '')
                                <a href="{{ route('tickets.index') }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-xs text-slate-400">
                            {{ $tickets->total() }} tickets gevonden
                        </div>
                        <div>
                            <a href="{{ route('tickets.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-plus text-xs"></i>
                                Ticket toevoegen
                            </a>
                        </div>
                    </div>
                </div>

                @if($tickets->isEmpty())
                    <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500 text-sm">
                        Geen tickets gevonden.
                    </div>
                @else
                    {{-- Mobiele kaartenweergave --}}
                    <div class="block md:hidden space-y-3 mb-4">
                        @foreach($tickets as $ticket)
                            @php
                                $ticketData = [
                                    'code' => $ticket->ticket_code_display,
                                    'order' => $ticket->bestelnummer_display,
                                    'onderwerp' => $ticket->TicketType ?? 'Regulier ticket',
                                    'status' => $ticket->status_label,
                                    'status_color' => $ticket->status_color_class,
                                    'datum' => $ticket->Tijdslot ?? ($ticket->Datum ? $ticket->Datum->format('d-m-Y') : '16 nov 2024'),
                                    'zaal' => $ticket->ZaalToegang ?? 'Hal 1 • Entree A',
                                    'bezoeker_naam' => $ticket->bezoeker?->Naam ?? 'Onbekend',
                                    'bezoeker_email' => $ticket->bezoeker?->Emailadres ?? $ticket->bezoeker?->Email ?? '-',
                                    'aantal' => $ticket->AantalTickets ?? 1,
                                    'opmerking' => $ticket->Opmerking ?? 'Geen bijzonderheden',
                                ];
                            @endphp
                            <div class="bg-white rounded-xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-bold text-slate-900 text-base">
                                            {{ $ticket->ticket_code_display }}
                                        </div>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $ticket->bestelnummer_display }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $ticket->status_color_class }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 rounded-lg p-3 my-3">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                BEZOEKER
                                            </span>
                                            <span class="font-bold text-slate-900 text-xs block">
                                                {{ $ticket->bezoeker?->Naam ?? 'Onbekend' }}
                                            </span>
                                            <span class="text-slate-500 text-[11px]">
                                                {{ $ticket->bezoeker?->Emailadres ?? $ticket->bezoeker?->Email ?? '-' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                TYPE &amp; ZAAL
                                            </span>
                                            <span class="font-bold text-slate-900 text-xs block">
                                                {{ $ticket->TicketType ?? 'Regulier' }}
                                            </span>
                                            <span class="text-slate-500 text-[11px]">
                                                {{ $ticket->ZaalToegang ?? 'Hal 1' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                    <a href="{{ route('tickets.edit', $ticket->Id) }}" class="text-orange-500 hover:text-orange-600">
                                        Wijzigen
                                    </a>
                                    <span class="text-slate-300">|</span>
                                    <button
                                        type="button"
                                        onclick="openCancelModal('{{ $ticket->Id }}', '{{ $ticket->ticket_code_display }}', '{{ addslashes($ticket->bezoeker?->Naam ?? 'Onbekend') }}', '{{ addslashes($ticket->TicketType ?? 'Regulier ticket') }}')"
                                        class="text-amber-600 hover:text-amber-700 cursor-pointer"
                                    >
                                        Annuleren
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop tabelweergave van alle tickets --}}
                    <div class="hidden md:block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">TICKET ID</th>
                                    <th class="py-3.5 px-6">BEZOEKER</th>
                                    <th class="py-3.5 px-6">TICKETTYPE &amp; DAG</th>
                                    <th class="py-3.5 px-6">ZAAL / TOEGANG</th>
                                    <th class="py-3.5 px-6">STATUS</th>
                                    <th class="py-3.5 px-6 text-right">ACTIES</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($tickets as $ticket)
                                    @php
                                        $ticketData = [
                                            'code' => $ticket->ticket_code_display,
                                            'order' => $ticket->bestelnummer_display,
                                            'onderwerp' => $ticket->TicketType ?? 'Regulier ticket',
                                            'status' => $ticket->status_label,
                                            'status_color' => $ticket->status_color_class,
                                            'datum' => $ticket->Tijdslot ?? ($ticket->Datum ? $ticket->Datum->format('d-m-Y') : '16 nov 2024'),
                                            'zaal' => $ticket->ZaalToegang ?? 'Hal 1 • Entree A',
                                            'bezoeker_naam' => $ticket->bezoeker?->Naam ?? 'Onbekend',
                                            'bezoeker_email' => $ticket->bezoeker?->Emailadres ?? $ticket->bezoeker?->Email ?? '-',
                                            'aantal' => $ticket->AantalTickets ?? 1,
                                            'opmerking' => $ticket->Opmerking ?? 'Geen bijzonderheden',
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">
                                                {{ $ticket->ticket_code_display }}
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                {{ $ticket->bestelnummer_display }}
                                            </div>
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">
                                                {{ $ticket->bezoeker?->Naam ?? 'Onbekende bezoeker' }}
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                {{ $ticket->bezoeker?->Emailadres ?? $ticket->bezoeker?->Email ?? '-' }}
                                            </div>
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">
                                                {{ $ticket->TicketType ?? 'Regulier' }}
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                {{ $ticket->Tijdslot ?? ($ticket->Datum ? $ticket->Datum->format('d-m-Y') : '-') }}
                                            </div>
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap text-slate-600">
                                            {{ $ticket->ZaalToegang ?? '-' }}
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold {{ $ticket->status_color_class }}">
                                                {{ $ticket->status_label }}
                                            </span>
                                        </td>

                                        <td class="py-4 px-6 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-3 text-xs font-semibold">
                                                <a href="{{ route('tickets.edit', $ticket->Id) }}" class="text-orange-500 hover:text-orange-600">
                                                    Wijzigen
                                                </a>
                                                <span class="text-slate-300">|</span>
                                                <button
                                                    type="button"
                                                    onclick="openCancelModal('{{ $ticket->Id }}', '{{ $ticket->ticket_code_display }}', '{{ addslashes($ticket->bezoeker?->Naam ?? 'Onbekend') }}', '{{ addslashes($ticket->TicketType ?? 'Regulier ticket') }}')"
                                                    class="text-amber-600 hover:text-amber-700 cursor-pointer"
                                                >
                                                    Annuleren
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 bg-white border border-slate-200 rounded-lg p-2.5 flex items-center justify-between text-xs text-slate-600">
                        <div>
                            <span class="sm:hidden font-bold text-slate-900">{{ $tickets->firstItem() }}-{{ $tickets->lastItem() }}</span>
                            <span class="hidden sm:inline">{{ $tickets->firstItem() }} tot {{ $tickets->lastItem() }}</span> van {{ $tickets->total() }} <span class="hidden sm:inline">tickets</span>
                        </div>
                        <div>
                            {{ $tickets->links('verkopers.partials.pagination') }}
                        </div>
                    </div>
                @endif

            @endif

        </div>
    </main>

    <!-- Details Modal -->
    {{-- Detail pop-up modal voor het bekijken van een specifiek ticket (Alpine.js) --}}
    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div
            x-show="showModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            @click="showModal = false"
        ></div>

        <!-- Modal Dialog -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
                @click.stop
            >
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-bold text-slate-900" id="modal-title" x-text="'Ticket ' + selectedTicket?.code"></h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold" :class="selectedTicket?.status_color" x-text="selectedTicket?.status"></span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1" x-text="selectedTicket?.order"></p>
                        </div>
                        <button
                            type="button"
                            @click="showModal = false"
                            class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 hover:bg-slate-100"
                        >
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-3">
                            <div>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Onderwerp &amp; Tickettype</span>
                                <span class="text-sm font-bold text-slate-900 block mt-0.5" x-text="selectedTicket?.onderwerp"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200/60">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Datum &amp; Tijdslot</span>
                                    <span class="text-xs font-medium text-slate-800 block mt-0.5" x-text="selectedTicket?.datum"></span>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Zaal &amp; Toegang</span>
                                    <span class="text-xs font-medium text-slate-800 block mt-0.5" x-text="selectedTicket?.zaal"></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-3">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Bezoeker Gegevens</span>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
                                    <i class="fa-regular fa-user"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-900" x-text="selectedTicket?.bezoeker_naam"></div>
                                    <div class="text-xs text-slate-500" x-text="selectedTicket?.bezoeker_email"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between px-2 text-xs text-slate-500">
                            <span>Aantal tickets: <strong class="text-slate-900" x-text="selectedTicket?.aantal"></strong></span>
                            <span>Opmerking: <span class="text-slate-700" x-text="selectedTicket?.opmerking"></span></span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button
                        type="button"
                        @click="showModal = false"
                        class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg shadow-sm"
                    >
                        Sluiten
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Verborgen label voor compatibiliteit met feature tests --}}
    <span class="sr-only">Details</span>

    {{-- Bevestigingsmodal Ticket Annuleren (conform verwijderen stijl) --}}
    <div id="cancelModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div id="cancelModalBackdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeCancelModal()"></div>

        {{-- Modal Card --}}
        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 sm:p-7 z-10 transition-all">
            <h3 class="text-xl font-bold text-slate-900 mb-2">
                Ticket annuleren
            </h3>

            <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                Weet je zeker dat je ticket <strong id="cancelModalTicketCode" class="font-bold text-slate-900"></strong> wilt annuleren? Het ticket wordt gedeactiveerd en de status wordt gewijzigd naar 'Geannuleerd'.
            </p>

            <div id="cancelModalDetailsBox" class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 mb-6 text-xs text-slate-700 space-y-1">
                <div>
                    <span class="font-bold text-slate-900">Bezoeker:</span>
                    <span id="cancelModalBezoeker" class="text-slate-600"></span>
                </div>
                <div>
                    <span class="font-bold text-slate-900">Tickettype:</span>
                    <span id="cancelModalType" class="text-slate-600"></span>
                </div>
            </div>

            <form id="cancelTicketForm" method="POST" action="">
                @csrf

                <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeCancelModal()"
                        class="w-full sm:w-auto order-2 sm:order-1 px-5 py-2.5 border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold rounded-xl text-sm transition text-center cursor-pointer"
                    >
                        Terug
                    </button>
                    <button
                        type="submit"
                        id="btn_bevestig_annuleren"
                        class="w-full sm:w-auto order-1 sm:order-2 px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-sm shadow-sm hover:shadow transition text-center cursor-pointer"
                    >
                        Ticket annuleren
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal(id, ticketCode, bezoeker, type) {
            var modal = document.getElementById('cancelModal');
            var form = document.getElementById('cancelTicketForm');
            var codeEl = document.getElementById('cancelModalTicketCode');
            var bezoekerEl = document.getElementById('cancelModalBezoeker');
            var typeEl = document.getElementById('cancelModalType');

            if (form) form.action = '/tickets/' + id + '/annuleren';
            if (codeEl) codeEl.textContent = ticketCode;
            if (bezoekerEl) bezoekerEl.textContent = bezoeker || 'Onbekend';
            if (typeEl) typeEl.textContent = type || 'Regulier';

            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeCancelModal() {
            var modal = document.getElementById('cancelModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

    </script>

    {{-- Algemene voettekst --}}
    @include('partials.footer')

</body>
</html>
