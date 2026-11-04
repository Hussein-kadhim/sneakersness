<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event wijzigen &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Wijzig de gegevens van een Sneakerness evenement.">

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
                Terug naar events overzicht
            </a>

            <div class="mb-6 sm:mb-8">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Event wijzigen</h1>
                <p class="text-sm sm:text-base text-slate-500 mt-2">Pas de gegevens van de Sneakerness editie aan.</p>
            </div>

            {{-- Toon databaseproblemen ook na een redirect terug vanaf de updateactie. --}}
            @if($dbError || session('databaseError'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800" role="alert">
                    <div class="flex items-center gap-2 font-semibold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Er is een probleem met de database. Probeer het later opnieuw.</span>
                    </div>
                </div>
            @endif

            @if($event && !$dbError)
                {{-- De event-ID in de route bepaalt welk record wordt bijgewerkt. --}}
                <form method="POST" action="{{ route('events.update', $event->Id) }}" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    @csrf
                    {{-- HTML-formulieren ondersteunen PUT niet rechtstreeks; Laravel leest deze method override. --}}
                    @method('PUT')

                    {{-- Validatiefouten blijven zichtbaar bovenaan het formulier. --}}
                    @if($errors->any())
                        <div class="m-5 sm:m-8 mb-0 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800" role="alert">
                            <p class="font-semibold mb-1">Controleer de ingevulde gegevens.</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Vul het formulier met oude invoer of anders met de opgeslagen evenementwaarden. --}}
                    <div class="p-5 sm:p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="Naam" class="block text-sm font-semibold text-slate-700 mb-1.5">Eventnaam <span class="text-orange-500">*</span></label>
                                <input id="Naam" name="Naam" type="text" value="{{ old('Naam', $event->Naam) }}" maxlength="100" required
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Naam') border-red-500 @enderror">
                                @error('Naam') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="OrganisatorId" class="block text-sm font-semibold text-slate-700 mb-1.5">Organisator <span class="text-orange-500">*</span></label>
                                <select id="OrganisatorId" name="OrganisatorId" required
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('OrganisatorId') border-red-500 @enderror">
                                    @foreach($organisatoren as $organisator)
                                        <option value="{{ $organisator->Id }}" @selected(old('OrganisatorId', $event->OrganisatorId) == $organisator->Id)>{{ $organisator->Naam }}</option>
                                    @endforeach
                                </select>
                                @error('OrganisatorId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="Locatie" class="block text-sm font-semibold text-slate-700 mb-1.5">Locatie / Venue <span class="text-orange-500">*</span></label>
                                <input id="Locatie" name="Locatie" type="text" value="{{ old('Locatie', $event->Locatie) }}" maxlength="150" required
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Locatie') border-red-500 @enderror">
                                @error('Locatie') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                {{-- De opgeslagen datum wordt naar het formaat van een HTML-datumveld gezet. --}}
                                <label for="Datum" class="block text-sm font-semibold text-slate-700 mb-1.5">Datum <span class="text-orange-500">*</span></label>
                                <input id="Datum" name="Datum" type="date" value="{{ old('Datum', $event->Datum?->format('Y-m-d')) }}" required
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Datum') border-red-500 @enderror">
                                @error('Datum') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="AantalTicketsPerTijdslot" class="block text-sm font-semibold text-slate-700 mb-1.5">Verwachte bezoekers / capaciteit <span class="text-orange-500">*</span></label>
                                <input id="AantalTicketsPerTijdslot" name="AantalTicketsPerTijdslot" type="number" min="1" step="1" value="{{ old('AantalTicketsPerTijdslot', $event->AantalTicketsPerTijdslot) }}" required
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('AantalTicketsPerTijdslot') border-red-500 @enderror">
                                @error('AantalTicketsPerTijdslot') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="BeschikbareStands" class="block text-sm font-semibold text-slate-700 mb-1.5">Beschikbare stands <span class="text-orange-500">*</span></label>
                                <input id="BeschikbareStands" name="BeschikbareStands" type="number" min="0" step="1" value="{{ old('BeschikbareStands', $event->BeschikbareStands) }}" required
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('BeschikbareStands') border-red-500 @enderror">
                                @error('BeschikbareStands') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="IsActief" class="block text-sm font-semibold text-slate-700 mb-1.5">Status <span class="text-orange-500">*</span></label>
                                <select id="IsActief" name="IsActief" required
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('IsActief') border-red-500 @enderror">
                                    <option value="1" @selected((string) old('IsActief', (int) $event->IsActief) === '1')>Actief</option>
                                    <option value="0" @selected((string) old('IsActief', (int) $event->IsActief) === '0')>Gepland</option>
                                </select>
                                @error('IsActief') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                {{-- Maak de omschrijving leeg en sla op om de bestaande opmerking te verwijderen. --}}
                                <label for="Opmerking" class="block text-sm font-semibold text-slate-700 mb-1.5">Korte omschrijving / opmerkingen</label>
                                <textarea id="Opmerking" name="Opmerking" rows="4" maxlength="250"
                                    class="w-full rounded-lg border-slate-200 text-sm focus:border-orange-500 focus:ring-orange-500 @error('Opmerking') border-red-500 @enderror">{{ old('Opmerking', $event->Opmerking) }}</textarea>
                                @error('Opmerking') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Annuleren verlaat het formulier zonder de opgeslagen gegevens te wijzigen. --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 px-5 sm:px-8 py-5 bg-slate-50 border-t border-slate-200">
                        <a href="{{ route('events.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-600 hover:bg-white">
                            Annuleren
                        </a>
                        <button type="submit" class="inline-flex justify-center items-center gap-2 px-5 py-2.5 bg-orange-500 hover:bg-orange-600 rounded-lg text-sm font-semibold text-white shadow-sm">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Wijzigingen opslaan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
