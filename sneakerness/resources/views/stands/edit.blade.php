{{-- Pagina voor het bewerken en wijzigen van een gereserveerde stand --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stand Wijzigen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Wijzig je standreservering voor Sneakerness Rotterdam.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/stands/standoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Terugknop naar overzicht --}}
            <div class="mb-5">
                <a href="{{ route('stands.index') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Terug naar stand overzicht</span>
                </a>
            </div>

            {{-- Pagina titel en toelichting --}}
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Stand Reservering Wijzigen
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Pas de gegevens van je gereserveerde stand aan en sla de wijzigingen op.
                </p>
            </div>

            {{-- Succesmelding na actie --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center gap-3 shadow-sm" role="status">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <i class="fa-solid fa-circle-check text-base"></i>
                    </div>
                    <div>
                        <span class="font-bold block text-emerald-900 text-xs sm:text-sm">Gelukt</span>
                        <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Unhappy scenario foutmelding: Stand wijzigen is niet gelukt --}}
            @if(session('error') || $errors->any())
                <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-xl text-red-800 shadow-sm" role="alert">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600 shrink-0 mt-0.5">
                            <i class="fa-solid fa-circle-xmark text-base"></i>
                        </div>
                        <div class="flex-grow">
                            <span class="font-bold block text-red-900 text-sm sm:text-base">
                                {{ session('error') ?? 'Stand wijzigen is niet gelukt' }}
                            </span>
                            <p class="text-xs sm:text-sm text-red-700 mt-0.5">
                                Controleer de onderstaande invoervelden en vul geldige gegevens in.
                            </p>
                            @if($errors->any())
                                <ul class="mt-2.5 list-disc list-inside text-xs text-red-700 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Foutmelding bij database storing (unhappy scenario) --}}
            @if(!empty($errorMessage) || !empty($dbError))
                <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6 text-red-800 flex items-start gap-4 shadow-sm" role="alert">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0 text-red-600 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-red-900 mb-1">Verbindingsfout</h3>
                        <p class="text-sm text-red-700 leading-relaxed font-medium">
                            {{ $errorMessage ?? 'Database is momenteel niet beschikbaar, de standgegevens konden niet worden geladen. Probeer het later opnieuw.' }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Huidige reservering informatiepaneel --}}
            <div class="mb-6 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $stand->type_badge_class }}">
                                    Stand {{ $stand->StandType }}
                                </span>
                                <h2 class="text-lg font-bold text-slate-900">
                                    #{{ str_pad($stand->Id, 3, '0', STR_PAD_LEFT) }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $stand->location_display }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="text-left sm:text-right">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Gereserveerd voor</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900">
                                {{ $stand->verkoper ? $stand->verkoper->Naam : 'Onbekend' }}
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Gereserveerd
                        </span>
                    </div>
                </div>
            </div>

            {{-- Wijzigingsformulier --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('stands.update', $stand->Id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                            {{-- Stand Type --}}
                            <div>
                                <label for="StandType" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Stand Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="StandType"
                                    name="StandType"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-white border @error('StandType') border-red-300 ring-2 ring-red-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                >
                                    <option value="">-- Selecteer standtype --</option>
                                    <option value="AA+" {{ old('StandType', $stand->StandType) === 'AA+' ? 'selected' : '' }}>Stand AA+ (Premium &bull; Hal 1)</option>
                                    <option value="AA" {{ old('StandType', $stand->StandType) === 'AA' ? 'selected' : '' }}>Stand AA (Standaard &bull; Hal 1)</option>
                                    <option value="A" {{ old('StandType', $stand->StandType) === 'A' ? 'selected' : '' }}>Stand A (Basis &bull; Hal 2)</option>
                                </select>
                                @error('StandType')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Aantal Dagen --}}
                            <div>
                                <label for="AantalDagen" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Huurperiode (Dagen) <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="AantalDagen"
                                    name="AantalDagen"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-white border @error('AantalDagen') border-red-300 ring-2 ring-red-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                >
                                    <option value="1" {{ (string)old('AantalDagen', $stand->AantalDagen) === '1' ? 'selected' : '' }}>1 dag (Zaterdag)</option>
                                    <option value="2" {{ (string)old('AantalDagen', $stand->AantalDagen) === '2' ? 'selected' : '' }}>2 dagen (Geheel weekend)</option>
                                </select>
                                @error('AantalDagen')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Prijs --}}
                            <div>
                                <label for="Prijs" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Prijs (&euro; excl. BTW) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                                        &euro;
                                    </div>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="9999.99"
                                        id="Prijs"
                                        name="Prijs"
                                        value="{{ old('Prijs', number_format((float)$stand->Prijs, 2, '.', '')) }}"
                                        placeholder="bijv. 600.00"
                                        required
                                        class="w-full pl-8 pr-3.5 py-2.5 bg-white border @error('Prijs') border-red-300 ring-2 ring-red-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    >
                                </div>
                                @error('Prijs')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Locatie toelichting / Opmerking --}}
                            <div>
                                <label for="Opmerking" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Locatie / Standnummer toelichting
                                </label>
                                <input
                                    type="text"
                                    id="Opmerking"
                                    name="Opmerking"
                                    maxlength="250"
                                    value="{{ old('Opmerking', $stand->Opmerking) }}"
                                    placeholder="bijv. Stand #AA-102 &bull; Hal 1"
                                    class="w-full px-3.5 py-2.5 bg-white border @error('Opmerking') border-red-300 ring-2 ring-red-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                >
                                @error('Opmerking')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Knoppenbalk --}}
                        <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                            <a
                                href="{{ route('stands.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs sm:text-sm rounded-lg transition-colors"
                            >
                                Annuleren
                            </a>
                            <button
                                type="submit"
                                id="btn_stand_opslaan"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-sm transition-colors"
                            >
                                <i class="fa-solid fa-floppy-disk text-xs"></i>
                                Opslaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    {{-- Algemene voettekst --}}
    @include('partials.footer')

</body>
</html>

