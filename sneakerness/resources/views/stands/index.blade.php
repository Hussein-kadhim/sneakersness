<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stand Overzicht &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Beheer en bekijk alle stands, types, prijzen en bezetting voor Sneakerness Rotterdam 2024.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/stands/standoverzicht.css', 'resources/js/app.js'])
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
                    <a href="{{ route('contactpersonen.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                        Contactpersonen
                    </a>
                    <a href="{{ route('stands.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-orange-600 bg-orange-50 font-semibold rounded-lg">
                        Stands
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
                    Stand Overzicht
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    Beheer en bekijk alle stands, types, prijzen en bezetting voor Sneakerness Rotterdam 2024.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4 lg:hidden">
                <div class="bg-white rounded-xl p-3.5 border border-slate-200">
                    <span class="text-xs text-slate-400 font-medium block">Totaal stands</span>
                    <div class="mt-1 flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-slate-900">{{ $totalStands }}</span>
                        <span class="text-xs text-slate-400">stands</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-3.5 border border-slate-200">
                    <span class="text-xs text-slate-400 font-medium block">Verhuurd</span>
                    <div class="mt-1 flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-slate-900">{{ $rentedStandsCount }}</span>
                        <span class="text-xs text-slate-400">/ {{ $totalStands }}</span>
                    </div>
                </div>
            </div>

            <div class="hidden lg:grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs text-slate-500 font-medium block">Totaal stands</span>
                    <div class="text-3xl font-bold text-slate-900 mt-2">{{ $totalStands }}</div>
                </div>

                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs text-slate-500 font-medium block">Standtypes</span>
                    <div class="text-xl font-bold text-slate-900 mt-2">
                        {{ $countAAPlus }}× AA+ | {{ $countAA }}× AA | {{ $countA }}× A
                    </div>
                    <div class="text-xs text-slate-400 mt-1">Verdeling over Hal 1 &amp; 2</div>
                </div>

                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs text-slate-500 font-medium block">Verhuurd</span>
                    <div class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $rentedStandsCount }} <span class="text-xl font-bold text-slate-400">/ {{ $totalStands }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 border border-slate-200">
                    <span class="text-xs text-slate-500 font-medium block">Beschikbaar</span>
                    <div class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $availableStandsCount }} <span class="text-xl font-bold text-slate-400">stands</span>
                    </div>
                </div>
            </div>

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
