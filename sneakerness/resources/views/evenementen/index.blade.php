<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events Overzicht &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Beheer en bekijk alle actieve en geplande Sneakerness evenementen.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/contactpersonen/contactpersonenoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-[1720px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12">

            <div class="mb-6 sm:mb-8">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Events Overzicht
                </h1>
                <p class="text-sm sm:text-base text-slate-500 mt-2">
                    Beheer en bekijk alle geplande en actieve Sneakerness evenementen.
                </p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(!empty($errorMessage))
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 mb-6">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500 shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-red-900 text-sm sm:text-base">
                            Verbindingsfout
                        </h2>
                        <p class="text-red-700 text-xs sm:text-sm mt-0.5 leading-relaxed">
                            {{ $errorMessage }}
                        </p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 mb-4 lg:hidden">
                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <span class="text-xs text-slate-400 font-medium block">TOTAAL</span>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="text-3xl font-bold text-slate-900">{{ $totalEvents }}</span>
                            <span class="text-xs text-slate-400">events</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200">
                        <span class="text-xs text-slate-400 font-medium block">ACTIEF</span>
                        <div class="mt-1.5 flex items-baseline gap-1.5">
                            <span class="text-3xl font-bold text-slate-900">{{ $activeEvents }}</span>
                            <span class="text-xs text-slate-400">actief</span>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:grid grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs sm:text-sm text-slate-500 font-medium block">Totaal events</span>
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900 mt-2">{{ $totalEvents }}</div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs sm:text-sm text-slate-500 font-medium block">Actieve edities</span>
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900 mt-2">
                            {{ $activeEvents }} <span class="text-xl font-bold text-slate-400">actief</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs sm:text-sm text-slate-500 font-medium block">Verwachte bezoekers</span>
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900 mt-2">
                            {{ number_format($expectedVisitors, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs sm:text-sm text-slate-500 font-medium block">Locaties</span>
                        <div class="text-xl sm:text-2xl font-bold text-slate-900 mt-2 truncate" title="Van Nelle Fabriek Rotterdam">
                            Van Nelle Fabriek Rotterdam
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <form method="GET" action="{{ route('events.index') }}">
                        <div class="relative mb-3">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-sm"></i>
                            </div>
                            <input
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Zoek op eventnaam, stad of locatie..."
                                class="w-full pl-10 pr-10 py-3 bg-white border border-slate-200 rounded-lg text-sm sm:text-base text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                            @if($search !== '')
                                <a href="{{ route('events.index') }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-xs sm:text-sm text-slate-400">
                            {{ method_exists($events, 'total') ? $events->total() : count($events) }} events gevonden
                        </div>
                        <a href="#" onclick="return false;" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-lg shadow-sm">
                            <i class="fa-solid fa-plus text-xs"></i>
                            Event toevoegen
                        </a>
                    </div>
                </div>

                @if($events->isEmpty())
                    <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500 text-sm">
                        Geen events gevonden.
                    </div>
                @else
                    <div class="block md:hidden space-y-3 mb-4">
                        @foreach($events as $event)
                            @php
                                $eventDatum = $event->Datum instanceof \Carbon\Carbon
                                    ? $event->Datum->format('d-m-Y')
                                    : (is_string($event->Datum) ? date('d-m-Y', strtotime($event->Datum)) : '-');
                            @endphp
                            <div class="bg-white rounded-xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-base">
                                            {{ $event->Naam }}
                                        </h3>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $event->Opmerking ?? 'Sneakerness evenement' }}
                                        </p>
                                    </div>
                                    @if($event->IsActief)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Actief
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            Gepland
                                        </span>
                                    @endif
                                </div>

                                <div class="bg-slate-50 rounded-lg p-3 my-3 space-y-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 font-medium">DATUM</span>
                                        <span class="font-semibold text-slate-900">{{ $eventDatum }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 font-medium">LOCATIE</span>
                                        <span class="font-semibold text-slate-900">{{ $event->Locatie }}</span>
                                    </div>
                                </div>

                                <div class="flex gap-2 mt-3">
                                    <a href="#" onclick="return false;" class="w-full text-center py-2 px-3 border border-orange-200 text-orange-600 text-xs font-semibold rounded-lg hover:bg-orange-50">
                                        Wijzigen
                                    </a>
                                    <a href="#" onclick="return false;" class="w-full text-center py-2 px-3 border border-slate-200 text-slate-500 text-xs font-semibold rounded-lg hover:bg-slate-50">
                                        Verwijderen
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden md:block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">EVENTNAAM</th>
                                    <th class="py-3.5 px-6">DATUM</th>
                                    <th class="py-3.5 px-6">LOCATIE</th>
                                    <th class="py-3.5 px-6">CAPACITEIT / STATUS</th>
                                    <th class="py-3.5 px-6 text-right">ACTIES</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($events as $event)
                                    @php
                                        $eventDatum = $event->Datum instanceof \Carbon\Carbon
                                            ? $event->Datum->format('d-m-Y')
                                            : (is_string($event->Datum) ? date('d-m-Y', strtotime($event->Datum)) : '-');
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-bold text-slate-900">
                                                {{ $event->Naam }}
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                {{ $event->Opmerking ?? 'Sneakerness evenement' }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-slate-600">
                                            {{ $eventDatum }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-slate-600">
                                            {{ $event->Locatie }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            @if($event->IsActief)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Actief
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                    Gepland
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-3 text-xs">
                                                <a href="#" onclick="return false;" class="text-orange-500 hover:text-orange-600 font-semibold">
                                                    Wijzigen
                                                </a>
                                                <span class="text-slate-300">|</span>
                                                <a href="#" onclick="return false;" class="text-slate-600 hover:text-slate-900">
                                                    Verwijderen
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 bg-white border border-slate-200 rounded-lg p-2.5 flex items-center justify-between text-xs text-slate-600">
                        <div>
                            @if(method_exists($events, 'firstItem') && $events->firstItem())
                                <span class="sm:hidden font-bold text-slate-900">{{ $events->firstItem() }}-{{ $events->lastItem() }}</span>
                                <span class="hidden sm:inline">{{ $events->firstItem() }} tot {{ $events->lastItem() }}</span> van {{ $events->total() }} <span class="hidden sm:inline">events</span>
                            @else
                                1 tot {{ count($events) }} van {{ count($events) }} events
                            @endif
                        </div>
                        @if(method_exists($events, 'hasPages') && $events->hasPages())
                            <div>
                                {{ $events->links('verkopers.partials.pagination') }}
                            </div>
                        @endif
                    </div>
                @endif
            @endif

        </div>
    </main>

    @include('partials.footer')

</body>
</html>