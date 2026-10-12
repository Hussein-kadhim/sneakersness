{{-- Pagina voor het registreren en toevoegen van een nieuwe stand --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stand Toevoegen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Voeg een nieuwe stand toe aan het standoverzicht van Sneakerness Rotterdam.">

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
                    Nieuwe Stand Toevoegen
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Vul de gegevens in om een nieuwe stand aan de beursvloer toe te wijzen en beschikbaar te stellen.
                </p>
            </div>

            {{-- Formulierkaart --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('stands.store') }}" class="space-y-6">
                        @csrf

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
                                    <option value="AA+" {{ old('StandType') === 'AA+' ? 'selected' : '' }}>Stand AA+ (Premium &bull; Hal 1)</option>
                                    <option value="AA" {{ old('StandType') === 'AA' ? 'selected' : '' }}>Stand AA (Standaard &bull; Hal 1)</option>
                                    <option value="A" {{ old('StandType') === 'A' ? 'selected' : '' }}>Stand A (Basis &bull; Hal 2)</option>
                                </select>
                                @error('StandType')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Gekoppelde Verkoper --}}
                            <div>
                                <label for="VerkoperId" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Gekoppelde Verkoper <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="VerkoperId"
                                    name="VerkoperId"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-white border @error('VerkoperId') border-red-300 ring-2 ring-red-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                >
                                    <option value="">-- Kies een verkoper --</option>
                                    @foreach($verkopers as $verkoper)
                                        <option value="{{ $verkoper->Id }}" {{ (string)old('VerkoperId') === (string)$verkoper->Id ? 'selected' : '' }}>
                                            {{ $verkoper->Naam }} &ndash; {{ $verkoper->VerkooptSoort }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('VerkoperId')
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
                                        value="{{ old('Prijs') }}"
                                        placeholder="bijv. 450.00"
                                        required
                                        class="w-full pl-8 pr-3.5 py-2.5 bg-white border @error('Prijs') border-red-300 ring-2 ring-red-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                    >
                                </div>
                                @error('Prijs')
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
                                    <option value="1" {{ old('AantalDagen') === '1' ? 'selected' : '' }}>1 dag (Zaterdag)</option>
                                    <option value="2" {{ old('AantalDagen', '2') === '2' ? 'selected' : '' }}>2 dagen (Geheel weekend)</option>
                                </select>
                                @error('AantalDagen')
                                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Verhuurstatus --}}
                            <div>
                                <label for="VerhuurdStatus" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="VerhuurdStatus"
                                    name="VerhuurdStatus"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-white border @error('VerhuurdStatus') border-red-300 ring-2 ring-red-100 @else border-slate-200 @enderror rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                >
                                    <option value="0" {{ old('VerhuurdStatus', '0') === '0' ? 'selected' : '' }}>Beschikbaar</option>
                                    <option value="1" {{ old('VerhuurdStatus') === '1' ? 'selected' : '' }}>Verhuurd</option>
                                </select>
                                @error('VerhuurdStatus')
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
                                    value="{{ old('Opmerking') }}"
                                    placeholder="bijv. Stand #AA-105 &bull; Hal 1"
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
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs sm:text-sm rounded-lg shadow-sm transition-colors"
                            >
                                <i class="fa-solid fa-plus text-xs"></i>
                                Stand toevoegen
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
