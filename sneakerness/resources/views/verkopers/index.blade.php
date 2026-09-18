<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verkopers Overzicht &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Beheer en bekijk alle deelnemende verkopers en stands voor Sneakerness Rotterdam 2024.">

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
                    <a href="{{ route('tickets.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                        Tickets
                    </a>
                    <a href="{{ route('verkopers.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-orange-600 bg-orange-50 font-semibold rounded-lg">
                        Verkopers
                    </a>
                    <a href="{{ route('contactpersonen.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
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

    <main class="flex-grow py-5 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4 sm:mb-6">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Verkopers Overzicht
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    Beheer en bekijk alle deelnemende verkopers en stands voor Sneakerness Rotterdam 2024.
                </p>
            </div>

            @if(isset($dbError) && $dbError)
                @include('partials.database-error', ['itemType' => 'verkopers'])
            @else
                <div class="grid grid-cols-2 gap-3 mb-4 lg:hidden">
                    <div class="bg-white rounded-xl p-3.5 border border-slate-200">
                        <span class="text-xs text-slate-400 font-medium block">Totaal bezet</span>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-slate-900">{{ $rentedStandsCount }}</span>
                            <span class="text-xs text-slate-400">/ {{ $totalStandsCount }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-3.5 border border-slate-200">
                        <span class="text-xs text-slate-400 font-medium block">Partners &amp; AA+</span>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-slate-900">{{ $countAAPlus }}</span>
                            <span class="text-xs text-slate-400">AA+ stands</span>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Totaal verkopers</span>
                        <div class="text-3xl font-bold text-slate-900 mt-2">{{ $totalVerkopers }}</div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Standtypes</span>
                        <div class="text-xl font-bold text-slate-900 mt-2">
                            {{ $countAAPlus }}× AA+ | {{ $countAA }}× AA | {{ $countA }}× A
                        </div>
                        <div class="text-xs text-slate-400 mt-1">Verdeling over Hal 1 &amp; 2</div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Partners</span>
                        <div class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $partnerCount }} Partners
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-5 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Verhuurde stands</span>
                        <div class="text-3xl font-bold text-slate-900 mt-2">
                            {{ $rentedStandsCount }} <span class="text-xl font-bold text-slate-400">/ {{ $totalStandsCount }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <form method="GET" action="{{ route('verkopers.index') }}">
                        <div class="relative mb-3">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                            <input
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Zoek verkoper, stand..."
                                class="w-full pl-9 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                            @if($search !== '')
                                <a href="{{ route('verkopers.index') }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-xs text-slate-400">
                            {{ $verkopers->total() }} verkopers gevonden
                        </div>
                        <a href="#" onclick="return false;" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-sm">
                            <i class="fa-solid fa-plus text-xs"></i>
                            Verkoper toevoegen
                        </a>
                    </div>
                </div>

                @if($verkopers->isEmpty())
                <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500 text-sm">
                    Geen verkopers gevonden.
                </div>
            @else
                <div class="block md:hidden space-y-3 mb-4">
                    @foreach($verkopers as $verkoper)
                        @php
                            $stand = $verkoper->primary_stand;
                            $contact = $verkoper->primary_contact;
                        @endphp
                        <div class="bg-white rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base">
                                        {{ $verkoper->Naam }}
                                    </h3>
                                    @if($stand)
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $stand->location_display }}
                                        </p>
                                    @endif
                                </div>
                                <span class="w-2.5 h-2.5 rounded-full {{ in_array($verkoper->Opmerking, ['Actief', 'Bevestigd']) ? 'bg-emerald-500' : 'bg-amber-500' }} mt-1.5 shrink-0"></span>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3 my-3">
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                            TYPE &amp; DAGEN
                                        </span>
                                        <span class="text-slate-800 text-xs">
                                            <strong class="font-bold text-slate-900">{{ $verkoper->VerkooptSoort }}</strong>
                                            @if($stand)
                                                &bull; Stand {{ $stand->StandType }} ({{ $stand->days_short }})
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                            CONTACT
                                        </span>
                                        <span class="font-bold text-slate-900 text-xs">
                                            {{ $contact?->Naam ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                                @if($contact)
                                    <div class="text-xs text-slate-500 mt-2">
                                        {{ $contact->Email }} &bull; {{ $contact->Telefoonnummer }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                <a href="#" onclick="return false;" class="px-4 py-1.5 border border-slate-200 text-slate-700 text-xs font-medium rounded-lg hover:bg-slate-50">
                                    Wijzigen
                                </a>
                                <a href="#" onclick="return false;" class="px-4 py-1.5 border border-red-200 text-red-500 text-xs font-medium rounded-lg hover:bg-red-50">
                                    Verwijderen
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden md:block bg-white border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">BEDRIJFSNAAM</th>
                                <th class="py-3.5 px-6">SOORT VERKOPER</th>
                                <th class="py-3.5 px-6">STANDTYPE &amp; DAGEN</th>
                                <th class="py-3.5 px-6">CONTACTGEGEVENS</th>
                                <th class="py-3.5 px-6">STATUS</th>
                                <th class="py-3.5 px-6 text-right">ACTIES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($verkopers as $verkoper)
                                @php
                                    $stand = $verkoper->primary_stand;
                                    $contact = $verkoper->primary_contact;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="font-bold text-slate-900">
                                            {{ $verkoper->Naam }}
                                        </div>
                                        @if($stand)
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                {{ $stand->location_display }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold {{ $verkoper->category_color_class }}">
                                            {{ $verkoper->VerkooptSoort }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($stand)
                                            <div class="font-bold text-slate-900">
                                                Stand {{ $stand->StandType }}
                                            </div>
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                {{ $stand->days_text }}
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        @if($contact)
                                            <div class="font-medium text-slate-900">
                                                {{ $contact->Naam }}
                                            </div>
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                {{ $contact->Email }} &bull; {{ $contact->Telefoonnummer }}
                                            </div>
                                        @else
                                            <span class="text-slate-400">Geen contact</span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold {{ $verkoper->status_color_class }}">
                                            {{ $verkoper->status_label }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-3 text-xs">
                                            <a href="#" onclick="return false;" class="text-orange-500 hover:text-orange-600 font-semibold">
                                                Wijzigen
                                            </a>
                                            <span class="text-slate-300">|</span>
                                            <a href="#" onclick="return false;" class="text-slate-500 hover:text-slate-900">
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
                        <span class="sm:hidden font-bold text-slate-900">{{ $verkopers->firstItem() }}-{{ $verkopers->lastItem() }}</span>
                        <span class="hidden sm:inline">{{ $verkopers->firstItem() }} tot {{ $verkopers->lastItem() }}</span> van {{ $verkopers->total() }} <span class="hidden sm:inline">verkopers</span>
                    </div>
                    <div>
                        {{ $verkopers->links('verkopers.partials.pagination') }}
                    </div>
                </div>
            @endif
        @endif

        </div>
    </main>

    <footer class="py-6 text-center">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-xs text-slate-500">
                &copy; 2026 Sneakerness Rotterdam &ndash; Alle rechten voorbehouden
            </p>
        </div>
    </footer>

</body>
</html>
