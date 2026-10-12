<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contactpersoon aan verkoper koppelen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Koppel een bestaande contactpersoon aan een standhouder of exposant.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900 font-['Plus_Jakarta_Sans',sans-serif]">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Pagina header --}}
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    <span class="sm:hidden">Contactpersoon koppelen</span>
                    <span class="hidden sm:inline">Contactpersoon aan verkoper koppelen</span>
                </h1>
                <p class="text-xs sm:text-base text-slate-500 mt-1">
                    <span class="sm:hidden">Koppel een bestaande contactpersoon aan een exposant of stand.</span>
                    <span class="hidden sm:inline">Koppel een bestaande contactpersoon aan een standhouder of exposant.</span>
                </p>
            </div>

            {{-- Foutmelding banner (Unhappy scenario) --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-red-800 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Succesmelding banner (Happy scenario) --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3 text-emerald-800 text-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Hoofdkaart met formulier --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <form action="{{ route('contactpersonen.koppelen.store') }}" method="POST" class="space-y-6 sm:space-y-8">
                    @csrf

                    {{-- Sectie 1: Contactpersoon selecteren --}}
                    <div>
                        <h2 class="hidden sm:block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                            SELECTEER CONTACTPERSOON
                        </h2>

                        <div>
                            <label for="contactpersoon_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                <span class="sm:hidden normal-case font-bold text-slate-800 text-sm">Selecteer contactpersoon</span>
                                <span class="hidden sm:inline">CONTACTPERSOON</span>
                                <span class="text-orange-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="contactpersoon_id" id="contactpersoon_id" class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer pr-10">
                                    <option value="">Selecteer contactpersoon</option>
                                    @foreach($contactpersonen as $contactpersoon)
                                        @php
                                            $gekoppeldeVerkoper = $contactpersoon->verkopers->first();
                                            $statusTekst = $gekoppeldeVerkoper ? $gekoppeldeVerkoper->Naam : 'Geen verkoper gekoppeld';
                                            $isSelected = (string)old('contactpersoon_id', $geselecteerdeContactpersoonId ?? '') === (string)$contactpersoon->Id;
                                        @endphp
                                        <option value="{{ $contactpersoon->Id }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $contactpersoon->Naam }} ({{ $statusTekst }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sectie 2: Koppeling aan verkoper / stand --}}
                    <div class="pt-6 border-t border-slate-100">
                        <h2 class="hidden sm:block text-xs font-bold text-slate-700 uppercase tracking-wider mb-4">
                            KOPPELING AAN VERKOPER / STAND
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="verkoper_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    <span class="sm:hidden normal-case font-bold text-slate-800 text-sm">Koppelen aan verkoper / stand</span>
                                    <span class="hidden sm:inline">KIES EEN VERKOPER / STAND</span>
                                    <span class="text-orange-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="verkoper_id" id="verkoper_id" class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer pr-10">
                                        <option value="">Selecteer verkoper / stand</option>
                                        @foreach($verkopers as $verkoper)
                                            @php
                                                $stand = $verkoper->stands->first();
                                                $standTekst = $stand ? ' (' . ($stand->location_display ?? ('Stand #' . $stand->StandType . '-' . $stand->Id)) . ' • Hal 1)' : '';
                                                $isSelected = (string)old('verkoper_id', $geselecteerdeVerkoperId ?? '') === (string)$verkoper->Id;
                                            @endphp
                                            <option value="{{ $verkoper->Id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $verkoper->Naam }}{{ $standTekst }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="rol" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    <span class="sm:hidden normal-case font-bold text-slate-800 text-sm">Rol / Functie bij verkoper</span>
                                    <span class="hidden sm:inline">FUNCTIE / ROL BIJ DEZE VERKOPER</span>
                                </label>
                                <div class="relative">
                                    <select name="rol" id="rol" class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer pr-10">
                                        <option value="Standmanager" {{ old('rol', 'Standmanager') === 'Standmanager' ? 'selected' : '' }}>Standmanager</option>
                                        <option value="Eigenaar" {{ old('rol') === 'Eigenaar' ? 'selected' : '' }}>Eigenaar</option>
                                        <option value="Sales" {{ old('rol') === 'Sales' ? 'selected' : '' }}>Sales</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Primair aanspreekpunt checkbox --}}
                        <div class="mt-4 pt-1">
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="is_primair" value="1" {{ old('is_primair', '1') ? 'checked' : '' }} class="mt-1 h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500 accent-orange-600">
                                <div>
                                    <span class="text-sm font-semibold text-slate-800">
                                        Instellen als primair aanspreekpunt voor deze exposant
                                    </span>
                                    <span class="block text-xs text-slate-400 mt-0.5">
                                        (Dit contact wordt als eerste benaderd)
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Knoppen onderaan: Mobiel gestapeld (Koppeling opslaan boven, Annuleren onder); Desktop rechts uitgelijnd (Annuleren links, Koppeling opslaan rechts) --}}
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('contactpersonen.index') }}" class="order-2 sm:order-1 w-full sm:w-auto px-6 py-3 border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition text-center">
                            Annuleren
                        </a>
                        <button type="submit" style="background-color: #ea580c !important; color: #ffffff !important; display: inline-flex; align-items: center; justify-content: center;" class="order-1 sm:order-2 w-full sm:w-auto px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-xl shadow-sm transition text-center cursor-pointer">
                            Koppeling opslaan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>

    {{-- Footer --}}
    @include('partials.footer')

</body>
</html>
