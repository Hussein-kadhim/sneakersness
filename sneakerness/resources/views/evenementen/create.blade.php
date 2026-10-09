<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event toevoegen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Voeg een nieuw Sneakerness evenement toe.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/contactpersonen/contactpersonenoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900">
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-800 mb-5">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Terug naar events
            </a>

            <div class="mb-6 sm:mb-8">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Event toevoegen</h1>
                <p class="text-sm sm:text-base text-slate-500 mt-2">Vul de gegevens in om een nieuw evenement aan te maken.</p>
            </div>

            <form method="POST" action="{{ route('events.store') }}" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                @csrf

                <div class="p-5 sm:p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label for="Naam" class="block text-sm font-semibold text-slate-700 mb-1.5">Eventnaam <span class="text-orange-500">*</span></label>
                            <input id="Naam" name="Naam" type="text" value="{{ old('Naam') }}" maxlength="100" required autocomplete="off"
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Naam') border-red-500 @enderror">
                            @error('Naam') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="OrganisatorId" class="block text-sm font-semibold text-slate-700 mb-1.5">Organisator <span class="text-orange-500">*</span></label>
                            <select id="OrganisatorId" name="OrganisatorId" required
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('OrganisatorId') border-red-500 @enderror">
                                <option value="">Selecteer een organisator</option>
                                @foreach($organisatoren as $organisator)
                                    <option value="{{ $organisator->Id }}" @selected(old('OrganisatorId') == $organisator->Id)>{{ $organisator->Naam }}</option>
                                @endforeach
                            </select>
                            @error('OrganisatorId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="Datum" class="block text-sm font-semibold text-slate-700 mb-1.5">Datum <span class="text-orange-500">*</span></label>
                            <input id="Datum" name="Datum" type="date" value="{{ old('Datum') }}" required
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Datum') border-red-500 @enderror">
                            @error('Datum') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="Locatie" class="block text-sm font-semibold text-slate-700 mb-1.5">Locatie <span class="text-orange-500">*</span></label>
                            <input id="Locatie" name="Locatie" type="text" value="{{ old('Locatie') }}" maxlength="150" required
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Locatie') border-red-500 @enderror">
                            @error('Locatie') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="AantalTicketsPerTijdslot" class="block text-sm font-semibold text-slate-700 mb-1.5">Tickets per tijdslot <span class="text-orange-500">*</span></label>
                            <input id="AantalTicketsPerTijdslot" name="AantalTicketsPerTijdslot" type="number" min="1" step="1" value="{{ old('AantalTicketsPerTijdslot') }}" required
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('AantalTicketsPerTijdslot') border-red-500 @enderror">
                            @error('AantalTicketsPerTijdslot') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="BeschikbareStands" class="block text-sm font-semibold text-slate-700 mb-1.5">Beschikbare stands <span class="text-orange-500">*</span></label>
                            <input id="BeschikbareStands" name="BeschikbareStands" type="number" min="0" step="1" value="{{ old('BeschikbareStands') }}" required
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('BeschikbareStands') border-red-500 @enderror">
                            @error('BeschikbareStands') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="IsActief" class="block text-sm font-semibold text-slate-700 mb-1.5">Status <span class="text-orange-500">*</span></label>
                            <select id="IsActief" name="IsActief" required
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('IsActief') border-red-500 @enderror">
                                <option value="1" @selected(old('IsActief', '1') === '1')>Actief</option>
                                <option value="0" @selected(old('IsActief') === '0')>Gepland</option>
                            </select>
                            @error('IsActief') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="Opmerking" class="block text-sm font-semibold text-slate-700 mb-1.5">Omschrijving <span class="text-slate-400 font-normal">(optioneel)</span></label>
                            <textarea id="Opmerking" name="Opmerking" rows="4" maxlength="250"
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Opmerking') border-red-500 @enderror">{{ old('Opmerking') }}</textarea>
                            @error('Opmerking') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 sm:px-8 py-5 bg-slate-50 border-t border-slate-200">
                    <a href="{{ route('events.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-600 hover:bg-white">
                        Annuleren
                    </a>
                    <button type="submit" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 rounded-lg text-sm font-semibold text-white shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Event toevoegen
                    </button>
                </div>
            </form>
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
