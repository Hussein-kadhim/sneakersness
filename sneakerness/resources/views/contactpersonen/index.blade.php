<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contactpersonen Overzicht &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Beheer en bekijk alle contactpersonen en hun koppeling aan exposanten voor Sneakerness Rotterdam 2024.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/contactpersonen/contactpersonenoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="overlay"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-500 flex items-center justify-center font-bold text-white text-base">
                            SN
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 hidden sm:inline">
                            Sneakerness Rotterdam
                        </span>
                        <span class="text-base font-bold tracking-tight text-slate-900 sm:hidden">
                            Sneakerness RTM
                        </span>
                    </a>
                </div>

                <nav class="navbar hidden md:flex items-center gap-1.5 text-sm font-medium">
                    <button type="button" class="close-menu md:hidden text-xl text-slate-400 hover:text-slate-700 self-end mb-2" aria-label="Sluiten">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <a href="{{ route('home') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                        Home
                    </a>
                    <a href="{{ route('verkopers.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                        Verkopers
                    </a>
                    <a href="{{ route('contactpersonen.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-orange-600 bg-orange-50 font-semibold rounded-lg">
                        Contactpersonen
                    </a>
                    <a href="#" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                        Stand huren
                    </a>
                    <a href="#" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                        Events
                    </a>
                    <a href="#" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                        Bezoekers
                    </a>

                    <div class="pt-3 mt-1.5 border-t border-slate-200 w-full flex flex-col gap-2 md:hidden">
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                                    Uitloggen
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="w-full px-3.5 py-1.5 text-slate-700 hover:text-slate-900 font-semibold rounded-lg">
                                Inloggen
                            </a>
                            <a href="{{ route('register') }}" class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg text-center">
                                Registreren
                            </a>
                        @endauth
                    </div>
                </nav>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-3">
                        @auth
                            <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-slate-500 hover:text-slate-900">
                                    Uitloggen
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-900 hover:text-orange-500 px-3 py-2">
                                Inloggen
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg">
                                Registreren
                            </a>
                        @endauth
                    </div>

                    <button type="button" class="hamburger md:hidden border border-slate-200 rounded-lg p-2 text-slate-700 hover:bg-slate-50 flex items-center justify-center" aria-label="Menu">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 sm:mb-8">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Contactpersonen Overzicht
                </h1>
                <p class="text-sm sm:text-base text-slate-500 mt-2">
                    Beheer en bekijk alle contactpersonen en hun koppeling aan exposanten voor Sneakerness Rotterdam 2024.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 lg:hidden">
                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <span class="text-xs text-slate-400 font-medium block">TOTAAL</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-3xl font-bold text-slate-900">{{ $totalContactpersonen }}</span>
                        <span class="text-xs text-slate-400">personen</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border border-slate-200">
                    <span class="text-xs text-slate-400 font-medium block">GEKOPPELD</span>
                    <div class="mt-1.5 flex items-baseline gap-1.5">
                        <span class="text-3xl font-bold text-slate-900">{{ $linkedCount }}</span>
                        <span class="text-xs text-slate-400">/ {{ $totalContactpersonen }}</span>
                    </div>
                </div>
            </div>

            <div class="hidden lg:grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs sm:text-sm text-slate-500 font-medium block">Totaal contactpersonen</span>
                    <div class="text-3xl sm:text-4xl font-bold text-slate-900 mt-2">{{ $totalContactpersonen }}</div>
                </div>

                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs sm:text-sm text-slate-500 font-medium block">Gekoppeld aan verkoper</span>
                    <div class="text-3xl sm:text-4xl font-bold text-slate-900 mt-2">
                        {{ $linkedCount }} <span class="text-xl font-bold text-slate-400">/ 52 stands</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs sm:text-sm text-slate-500 font-medium block">Primaire aanspreekpunten</span>
                    <div class="text-3xl sm:text-4xl font-bold text-slate-900 mt-2">
                        {{ $primaryCount }} <span class="text-base font-semibold text-slate-500">vertegenwoordigers</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs sm:text-sm text-slate-500 font-medium block">Ongekoppeld</span>
                    <div class="text-3xl sm:text-4xl font-bold text-slate-900 mt-2">
                        {{ $unlinkedCount }} <span class="text-xl font-bold text-slate-400">personen</span>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <form method="GET" action="{{ route('contactpersonen.index') }}">
                    <div class="relative mb-3">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </div>
                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Zoek op naam, e-mailadres, telefoonnummer of gekoppelde verkoper..."
                            class="w-full pl-10 pr-10 py-3 bg-white border border-slate-200 rounded-lg text-sm sm:text-base text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                        @if($search !== '')
                            <a href="{{ route('contactpersonen.index') }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-xs sm:text-sm text-slate-400">
                        {{ $contactpersonen->total() }} contactpersonen gevonden
                    </div>
                    <a href="#" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-lg shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Contactpersoon toevoegen
                    </a>
                </div>
            </div>

            @if($contactpersonen->isEmpty())
                <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500 text-sm">
                    Geen contactpersonen gevonden.
                </div>
            @else
                <div class="block md:hidden space-y-3 mb-4">
                    @foreach($contactpersonen as $contactpersoon)
                        @php
                            $verkoper = $contactpersoon->primary_verkoper;
                            $stand = $verkoper?->primary_stand;
                        @endphp
                        <div class="bg-white rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base">
                                        {{ $contactpersoon->Naam }}
                                        @if($contactpersoon->subtitle_tag)
                                            <span class="text-slate-400 text-xs font-normal">{{ $contactpersoon->subtitle_tag }}</span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $contactpersoon->role_display }}
                                    </p>
                                </div>
                                <span class="w-2.5 h-2.5 rounded-full {{ in_array($contactpersoon->status_label, ['Actief', 'Bevestigd']) ? 'bg-emerald-500' : 'bg-amber-500' }} mt-1.5 shrink-0"></span>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3 my-3 space-y-2">
                                <div class="flex items-start justify-between gap-2 text-xs">
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                            GEKOPPELDE VERKOPER
                                        </span>
                                        @if($verkoper)
                                            <span class="font-bold text-slate-900 text-xs block">
                                                {{ $verkoper->Naam }}
                                            </span>
                                            @if($stand)
                                                <span class="text-slate-500 text-[11px] block mt-0.5">
                                                    {{ $stand->location_display }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="font-bold text-slate-900 text-xs block">
                                                Geen verkoper gekoppeld
                                            </span>
                                            <span class="text-slate-400 text-[11px] block mt-0.5">
                                                (Nog niet toegewezen)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                            STATUS
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $contactpersoon->status_color_class }}">
                                            {{ $contactpersoon->status_label }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs text-slate-600 pt-1.5 border-t border-slate-200/60">
                                    {{ $contactpersoon->Email }} &bull; {{ $contactpersoon->Telefoonnummer }}
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-2 mt-3">
                                <a href="#" class="w-full text-center py-2.5 px-3 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-50">
                                    Wijzigen
                                </a>
                                @if($verkoper)
                                    <a href="#" class="w-full text-center py-2.5 px-3 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-50">
                                        Ontkoppelen
                                    </a>
                                @else
                                    <a href="#" class="w-full text-center py-2.5 px-3 border border-orange-200 text-orange-600 text-xs font-semibold rounded-lg hover:bg-orange-50">
                                        Koppelen
                                    </a>
                                @endif
                                <a href="#" class="w-full text-center py-2.5 px-3 border border-red-200 text-red-500 text-xs font-semibold rounded-lg hover:bg-red-50">
                                    Verwijderen
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden md:block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse table-fixed">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-4 px-3.5 lg:px-4 w-[18%]">NAAM CONTACTPERSOON</th>
                                <th class="py-4 px-3.5 lg:px-4 w-[18%]">GEKOPPELDE VERKOPER</th>
                                <th class="py-4 px-3.5 lg:px-4 w-[15%]">FUNCTIE / ROL</th>
                                <th class="py-4 px-3.5 lg:px-4 w-[19%]">CONTACTGEGEVENS</th>
                                <th class="py-4 px-2 w-[9%]">STATUS</th>
                                <th class="py-4 px-3.5 lg:px-4 w-[21%] text-right">ACTIES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($contactpersonen as $contactpersoon)
                                @php
                                    $verkoper = $contactpersoon->primary_verkoper;
                                    $stand = $verkoper?->primary_stand;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="py-4.5 px-3.5 lg:px-4">
                                        <div class="font-bold text-slate-900 text-sm sm:text-base leading-snug">
                                            {{ $contactpersoon->Naam }}
                                            @if($contactpersoon->subtitle_tag)
                                                <span class="text-slate-400 font-normal text-xs sm:text-sm ml-1">{{ $contactpersoon->subtitle_tag }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="py-4.5 px-3.5 lg:px-4">
                                        @if($verkoper)
                                            <div class="font-bold text-slate-900 text-sm sm:text-base truncate">
                                                {{ $verkoper->Naam }}
                                            </div>
                                            @if($stand)
                                                <div class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate">
                                                    {{ $stand->location_display }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="font-bold text-slate-900 text-sm sm:text-base truncate">
                                                Geen verkoper gekoppeld
                                            </div>
                                            <div class="text-xs sm:text-sm text-slate-400 mt-0.5 truncate">
                                                (Nog niet toegewezen)
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-4.5 px-3.5 lg:px-4">
                                        <div class="text-slate-900 text-sm sm:text-base font-medium truncate">
                                            {{ $contactpersoon->role_display }}
                                        </div>
                                    </td>

                                    <td class="py-4.5 px-3.5 lg:px-4">
                                        <div class="text-xs sm:text-sm text-slate-600 truncate">
                                            <span>{{ $contactpersoon->Email }}</span>
                                            <span class="text-slate-300 mx-1">&bull;</span>
                                            <span class="text-slate-500">{{ $contactpersoon->Telefoonnummer }}</span>
                                        </div>
                                    </td>

                                    <td class="py-4.5 px-2 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold {{ $contactpersoon->status_color_class }}">
                                            {{ $contactpersoon->status_label }}
                                        </span>
                                    </td>

                                    <td class="py-4.5 px-3.5 lg:px-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2.5 text-xs sm:text-[13px]">
                                            <a href="#" class="text-orange-500 hover:text-orange-600 font-semibold">
                                                Wijzigen
                                            </a>
                                            @if($verkoper)
                                                <a href="#" class="text-slate-500 hover:text-slate-900">
                                                    Ontkoppelen
                                                </a>
                                            @else
                                                <a href="#" class="text-orange-500 hover:text-orange-600 font-semibold">
                                                    Koppelen
                                                </a>
                                            @endif
                                            <a href="#" class="text-slate-500 hover:text-slate-900">
                                                Verwijderen
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 bg-white border border-slate-200 rounded-lg p-3 flex items-center justify-between text-xs sm:text-sm text-slate-600">
                    <div>
                        <span class="sm:hidden font-bold text-slate-900">{{ $contactpersonen->firstItem() }}-{{ $contactpersonen->lastItem() }}</span>
                        <span class="hidden sm:inline">{{ $contactpersonen->firstItem() }} tot {{ $contactpersonen->lastItem() }}</span> van {{ $contactpersonen->total() }} <span class="hidden sm:inline">contactpersonen</span>
                    </div>
                    <div>
                        {{ $contactpersonen->links('verkopers.partials.pagination') }}
                    </div>
                </div>
            @endif

        </div>
    </main>

    <footer class="py-6 text-center">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-xs sm:text-sm text-slate-500">
                &copy; 2026 Sneakerness Rotterdam &ndash; Alle rechten voorbehouden
            </p>
        </div>
    </footer>

</body>
</html>
