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

    @include('partials.header')


    <main class="flex-grow py-5 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4 sm:mb-6">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Stand Overzicht
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    Bekijk en beheer alle geregistreerde stands en zie direct welke stands beschikbaar of verhuurd zijn voor Sneakerness Rotterdam 2024.
                </p>
            </div>

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

                <div class="mb-4">
                    <form method="GET" action="{{ route('stands.index') }}">
                        <div class="relative mb-3">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </div>
                            <input
                                type="text"
                                name="q"
                                value="{{ $search ?? '' }}"
                                placeholder="Zoek op standtype, hal, verkoper of opmerking..."
                                class="w-full pl-9 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            >
                            @if(!empty($search))
                                <a href="{{ route('stands.index') }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="text-xs text-slate-400">
                            {{ $stands->total() }} stands gevonden
                        </div>
                        <a href="#" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-sm">
                            <i class="fa-solid fa-plus text-xs"></i>
                            Stand toevoegen
                        </a>
                    </div>
                </div>

                @if($stands->isEmpty())
                    <div class="bg-white border border-slate-200 rounded-xl p-8 text-center text-slate-500 text-sm">
                        Geen stands gevonden.
                    </div>
                @else
                    <div class="block md:hidden space-y-3 mb-4">
                        @foreach($stands as $stand)
                            <div class="bg-white rounded-xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $stand->type_badge_class }}">
                                                Stand {{ $stand->StandType }}
                                            </span>
                                            <h3 class="font-bold text-slate-900 text-base">
                                                #{{ str_pad($stand->Id, 3, '0', STR_PAD_LEFT) }}
                                            </h3>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $stand->location_display }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-semibold {{ $stand->status_color_class }}">
                                        {{ $stand->status_label }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 rounded-lg p-3 my-3">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                VERKOPER
                                            </span>
                                            @if($stand->verkoper)
                                                <span class="font-bold text-slate-900 text-xs block">
                                                    {{ $stand->verkoper->Naam }}
                                                </span>
                                                <span class="text-slate-500 text-[11px] block mt-0.5">
                                                    {{ $stand->verkoper->VerkooptSoort }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-xs italic">
                                                    Niet toegewezen
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                PRIJS &amp; DAGEN
                                            </span>
                                            <span class="font-bold text-slate-900 text-xs block">
                                                {{ $stand->formatted_price }}
                                            </span>
                                            <span class="text-slate-500 text-[11px] block mt-0.5">
                                                {{ $stand->days_text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2">
                                    <a href="#" class="px-4 py-1.5 border border-slate-200 text-slate-700 text-xs font-medium rounded-lg hover:bg-slate-50">
                                        Wijzigen
                                    </a>
                                    <a href="#" class="px-4 py-1.5 border border-red-200 text-red-500 text-xs font-medium rounded-lg hover:bg-red-50">
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
                                    <th class="py-4 px-4 w-[22%]">STAND</th>
                                    <th class="py-4 px-4 w-[24%]">GEKOPPELDE VERKOPER</th>
                                    <th class="py-4 px-4 w-[16%]">DAGEN</th>
                                    <th class="py-4 px-4 w-[14%]">PRIJS</th>
                                    <th class="py-4 px-3 w-[12%]">STATUS</th>
                                    <th class="py-4 px-4 w-[12%] text-right">ACTIES</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @foreach($stands as $stand)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $stand->type_badge_class }}">
                                                    Stand {{ $stand->StandType }}
                                                </span>
                                                <span class="font-bold text-slate-900 text-sm sm:text-base">
                                                    #{{ str_pad($stand->Id, 3, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-slate-500 mt-1 truncate">
                                                {{ $stand->location_display }}
                                            </div>
                                        </td>

                                        <td class="py-4 px-4">
                                            @if($stand->verkoper)
                                                <div class="font-bold text-slate-900 text-sm sm:text-base truncate">
                                                    {{ $stand->verkoper->Naam }}
                                                </div>
                                                <div class="text-xs text-slate-500 mt-0.5 truncate">
                                                    {{ $stand->verkoper->VerkooptSoort }}
                                                </div>
                                            @else
                                                <div class="font-bold text-slate-400 text-sm sm:text-base italic">
                                                    Geen verkoper gekoppeld
                                                </div>
                                                <div class="text-xs text-slate-400 mt-0.5">
                                                    (Nog beschikbaar)
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-4 px-4">
                                            <div class="font-medium text-slate-900 text-sm">
                                                {{ $stand->days_text }}
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                {{ $stand->AantalDagen }} {{ $stand->AantalDagen == 1 ? 'dag' : 'dagen' }}
                                            </div>
                                        </td>

                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900 text-sm sm:text-base">
                                                {{ $stand->formatted_price }}
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5">
                                                excl. BTW
                                            </div>
                                        </td>

                                        <td class="py-4 px-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold {{ $stand->status_color_class }}">
                                                {{ $stand->status_label }}
                                            </span>
                                        </td>

                                        <td class="py-4 px-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-2.5 text-xs sm:text-[13px]">
                                                <a href="#" class="text-orange-500 hover:text-orange-600 font-semibold">
                                                    Wijzigen
                                                </a>
                                                <span class="text-slate-300">|</span>
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
                            <span class="sm:hidden font-bold text-slate-900">{{ $stands->firstItem() }}-{{ $stands->lastItem() }}</span>
                            <span class="hidden sm:inline">{{ $stands->firstItem() }} tot {{ $stands->lastItem() }}</span> van {{ $stands->total() }} <span class="hidden sm:inline">stands</span>
                        </div>
                        <div>
                            {{ $stands->links('stands.partials.pagination') }}
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
