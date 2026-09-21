<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events Overzicht &ndash; Sneakerness Rotterdam</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/verkopers/verkopersoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="overlay"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-500 flex items-center justify-center font-bold text-white text-base">SN</div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 hidden sm:inline">Sneakerness Rotterdam</span>
                    <span class="text-base font-bold tracking-tight text-slate-900 sm:hidden">Sneakerness RTM</span>
                </a>
                <nav class="navbar hidden md:flex items-center gap-1.5 text-sm font-medium">
                    <button type="button" class="close-menu md:hidden text-xl text-slate-400 hover:text-slate-700 self-end mb-2" aria-label="Sluiten"><i class="fa-solid fa-xmark"></i></button>
                    <a href="{{ route('home') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Home</a>
                    <a href="{{ route('tickets.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Tickets</a>
                    <a href="{{ route('verkopers.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Verkopers</a>
                    <a href="{{ route('contactpersonen.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Contactpersonen</a>
                    <a href="#" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Stand huren</a>
                    <a href="{{ route('events.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-orange-600 bg-orange-50 font-semibold rounded-lg">Events</a>
                    <a href="#" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Bezoekers</a>
                    <div class="pt-3 mt-1.5 border-t border-slate-200 w-full flex flex-col gap-2 md:hidden">
                        @if(empty($databaseError))
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="w-full">@csrf<button type="submit" class="w-full text-left px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Uitloggen</button></form>
                        @else
                            <a href="{{ route('login') }}" class="w-full px-3.5 py-1.5 text-slate-700 hover:text-slate-900 font-semibold rounded-lg">Inloggen</a>
                            <a href="{{ route('register') }}" class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg text-center">Registreren</a>
                        @endauth
                        @endif
                    </div>
                </nav>
                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-3">
                        @if(empty($databaseError))
                        @auth
                            <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-sm text-slate-500 hover:text-slate-900">Uitloggen</button></form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-900 hover:text-orange-500 px-3 py-2">Inloggen</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg">Registreren</a>
                        @endauth
                        @endif
                    </div>
                    <button type="button" class="hamburger md:hidden border border-slate-200 rounded-lg p-2 text-slate-700 hover:bg-slate-50 flex items-center justify-center" aria-label="Menu"><i class="fa-solid fa-bars text-lg"></i></button>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow bg-[#f7f8ff] py-8 sm:py-10 lg:py-12">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10">
            <div class="mb-7 sm:mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight text-[#0b1933]">Events Overzicht</h1>
                <p class="text-sm sm:text-base text-slate-500 mt-2">Beheer en bekijk alle geplande en actieve Sneakerness evenementen.</p>
            </div>

            @if(!empty($databaseError))
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-red-800 flex items-start gap-4 shadow-sm" role="alert">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0 text-red-600 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-lg" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-base text-red-900 mb-1">Verbindingsfout</h2>
                        <p class="text-sm text-red-700 leading-relaxed font-medium">Er is een probleem met de database. Probeer het later opnieuw.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
                    <div class="bg-white border border-[#e2e8fb] rounded-md px-5 py-5 min-h-[92px]"><span class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">Totaal events</span><div class="text-2xl font-extrabold text-[#12213c] mt-2">{{ $totalEvents }}</div></div>
                    <div class="bg-white border border-[#e2e8fb] rounded-md px-5 py-5 min-h-[92px]"><span class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">Actieve edities</span><div class="text-2xl font-extrabold text-[#12213c] mt-2">{{ $activeEvents }} <span class="text-sm font-medium text-slate-400">Actief</span></div></div>
                    <div class="bg-white border border-[#e2e8fb] rounded-md px-5 py-5 min-h-[92px]"><span class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">Verwachte bezoekers</span><div class="text-2xl font-extrabold text-[#12213c] mt-2">{{ number_format($expectedVisitors, 0, ',', '.') }}</div></div>
                    <div class="bg-white border border-[#e2e8fb] rounded-md px-5 py-5 min-h-[92px]"><span class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold">Locaties</span><div class="text-base font-extrabold text-[#12213c] mt-3 truncate" title="{{ $locations->implode(', ') }}">{{ $locations->first() ?? '-' }}@if($locations->count() > 1)<span class="font-medium text-slate-400"> e.a.</span>@endif</div></div>
                </div>

                <form method="GET" action="{{ route('events.index') }}" class="mb-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa-solid fa-magnifying-glass text-sm"></i></div>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Zoek op eventnaam, stad of locatie..." class="w-full pl-11 pr-10 py-3.5 bg-white border border-[#dfe6f7] rounded-md text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                        @if($search !== '')<a href="{{ route('events.index') }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600" aria-label="Zoekopdracht wissen"><i class="fa-solid fa-xmark"></i></a>@endif
                    </div>
                </form>

                <div class="flex justify-end mb-5">
                    <a href="#" onclick="return false;" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm px-4 py-2.5 rounded-md shadow-sm"><i class="fa-solid fa-plus text-xs"></i> Event toevoegen</a>
                </div>

                @if($events->isEmpty())
                    <div class="bg-white border border-[#dfe6f7] rounded-md p-10 text-center text-slate-500 text-sm">Geen events gevonden.</div>
                @else
                    <div class="text-xs text-slate-500 mb-2 md:hidden">{{ $events->total() }} events gevonden</div>
                    <div class="block md:hidden space-y-3">
                        @foreach($events as $event)
                            <article class="bg-white border border-[#dfe6f7] rounded-md p-4">
                                <div class="flex items-start justify-between gap-3"><div><h2 class="font-bold text-[#172642]">{{ $event->Naam }}</h2><p class="text-xs text-slate-400 mt-1">{{ $event->Locatie }}</p></div><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $event->IsActief ? 'bg-[#d9f5f2] text-[#258d85]' : 'bg-[#e8edff] text-[#6173ae]' }}"><span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $event->IsActief ? 'Actief' : 'Gepland' }}</span></div>
                                <div class="flex items-center justify-between border-t border-slate-100 mt-3 pt-3 text-xs text-slate-500"><span><i class="fa-regular fa-calendar mr-1.5"></i>{{ $event->Datum->format('d-m-Y') }}</span><span class="text-orange-500 font-semibold">Wijzigen</span></div>
                            </article>
                        @endforeach
                    </div>
                    <div class="hidden md:block bg-white border border-[#dfe6f7] rounded-md overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left border-collapse">
                            <thead><tr class="bg-[#e7edff] text-[11px] font-bold text-slate-500 uppercase tracking-wide"><th class="py-3.5 px-5">Eventnaam</th><th class="py-3.5 px-5">Datum</th><th class="py-3.5 px-5">Locatie</th><th class="py-3.5 px-5">Capaciteit / status</th><th class="py-3.5 px-5 text-right">Acties</th></tr></thead>
                            <tbody class="divide-y divide-[#e7edf9] text-sm">
                                @foreach($events as $event)
                                    <tr class="hover:bg-[#fbfcff]"><td class="py-4 px-5"><div class="font-bold text-[#172642]">{{ $event->Naam }}</div><div class="text-xs text-slate-400 mt-0.5">{{ $event->Opmerking ?? 'Sneakerness evenement' }}</div></td><td class="py-4 px-5 text-slate-600 whitespace-nowrap">{{ $event->Datum->format('d-m-Y') }}</td><td class="py-4 px-5 text-slate-600">{{ $event->Locatie }}</td><td class="py-4 px-5"><span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $event->IsActief ? 'bg-[#d9f5f2] text-[#258d85]' : 'bg-[#e8edff] text-[#6173ae]' }}"><span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $event->IsActief ? 'Actief' : 'Gepland' }}</span></td><td class="py-4 px-5 text-right whitespace-nowrap"><a href="#" onclick="return false;" class="text-orange-500 hover:text-orange-600 text-xs font-semibold">Wijzigen</a><span class="text-slate-300 mx-2">|</span><a href="#" onclick="return false;" class="text-orange-500 hover:text-orange-600 text-xs font-semibold">Verwijderen</a></td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="flex items-center justify-between border-t border-[#e7edf9] px-5 py-3 text-xs text-slate-500"><span>{{ $events->firstItem() ?? 0 }} tot {{ $events->lastItem() ?? 0 }} van {{ $events->total() }} events</span><div>{{ $events->links() }}</div></div>
                    </div>
                @endif
            @endif
        </div>
    </main>
</body>
</html>