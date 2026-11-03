<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contactpersoon Details &ndash; Sneakerness Rotterdam</title>
    <meta name="description" content="Bekijk de details en de gekoppelde standhouder van een contactpersoon.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/contactpersonen/contactpersonenoverzicht.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-900 font-['Plus_Jakarta_Sans',sans-serif]">

    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main class="flex-grow py-6 sm:py-10">
        <div class="w-full max-w-[860px] mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Terug link --}}
            <div class="mb-3">
                <a href="{{ route('contactpersonen.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Terug naar contactpersonen</span>
                </a>
            </div>

            {{-- Pagina titel en toelichting --}}
            <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Contactpersoon details
                    </h1>
                    <p class="text-xs sm:text-base text-slate-500 mt-1">
                        Inzicht in persoonsgegevens en koppeling aan exposant.
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('contactpersonen.edit', $contactpersoon->Id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition">
                        <i class="fa-solid fa-pen-to-square text-xs text-orange-500"></i>
                        <span>Wijzigen</span>
                    </a>
                </div>
            </div>

            {{-- Succesmelding banner --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3 text-emerald-800 text-sm">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Foutmelding banner (unhappy scenario) --}}
            @if(session('error') || $errors->has('general'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-red-800 text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-base flex-shrink-0"></i>
                    <span>{{ session('error') ?? $errors->first('general') }}</span>
                </div>
            @endif

            @php
                $verkoper = $contactpersoon->verkopers->first();
                $stand = $verkoper?->stands->first();
            @endphp

            {{-- Detail kaart --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">

                {{-- Persoonlijke gegevens --}}
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-regular fa-user text-slate-400 text-sm"></i>
                        <span>Persoonlijke gegevens</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Volledige naam</span>
                            <span class="text-sm font-bold text-slate-900">{{ $contactpersoon->Naam }}</span>
                            @if($contactpersoon->subtitle_tag)
                                <span class="text-xs text-slate-500 ml-1">{{ $contactpersoon->subtitle_tag }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Functie / Rol</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $contactpersoon->role_display }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold {{ $contactpersoon->status_color_class }}">
                                {{ $contactpersoon->status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Contactgegevens --}}
                <div class="pt-6 border-t border-slate-100">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-at text-slate-400 text-sm"></i>
                        <span>Contactgegevens</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">E-mailadres</span>
                            <span class="text-sm font-medium text-slate-900">{{ $contactpersoon->Email }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Telefoonnummer</span>
                            <span class="text-sm font-medium text-slate-900">{{ $contactpersoon->Telefoonnummer }}</span>
                        </div>
                    </div>
                </div>

                {{-- Gekoppelde Verkoper / Stand --}}
                <div class="pt-6 border-t border-slate-100">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-store text-slate-400 text-sm"></i>
                        <span>Koppeling aan Verkoper / Stand</span>
                    </h2>

                    @if($verkoper)
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Gekoppelde Exposant</span>
                                <span class="text-base font-bold text-slate-900">{{ $verkoper->Naam }}</span>
                                @if($stand)
                                    <span class="block text-xs text-slate-500 mt-0.5">{{ $stand->location_display }}</span>
                                @endif
                                @if(str_contains($verkoper->pivot->Opmerking ?? '', 'Hoofdcontactpersoon'))
                                    <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-0.5 rounded text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">
                                        <i class="fa-solid fa-star text-[10px]"></i>
                                        Primair aanspreekpunt
                                    </span>
                                @endif
                            </div>

                            {{-- Loskoppelen knop (opent dialoog) --}}
                            <div>
                                <button
                                    type="button"
                                    onclick="openLoskoppelModal('{{ $contactpersoon->Id }}', '{{ addslashes($contactpersoon->Naam) }}', '{{ addslashes($verkoper->Naam) }}', '{{ $verkoper->Id }}')"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-red-200 hover:bg-red-50 text-red-600 font-semibold text-xs sm:text-sm rounded-xl shadow-sm transition"
                                >
                                    <i class="fa-solid fa-link-slash text-xs"></i>
                                    <span>Loskoppelen</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center text-slate-500 text-sm">
                            <p class="font-medium text-slate-700">Geen verkoper gekoppeld</p>
                            <p class="text-xs text-slate-400 mt-1">Deze contactpersoon is momenteel niet toegewezen aan een stand of exposant.</p>
                            <div class="mt-4">
                                <a href="{{ route('contactpersonen.koppelen', ['contactpersoon_id' => $contactpersoon->Id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs sm:text-sm rounded-xl transition">
                                    <i class="fa-solid fa-link text-xs"></i>
                                    <span>Koppelen aan verkoper</span>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </main>

    {{-- Bevestigingsdialoog Modal voor Loskoppelen --}}
    <div id="loskoppelModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative animate-fadeIn">
            <h3 class="text-lg font-bold text-slate-900 mb-2">
                Contactpersoon loskoppelen
            </h3>
            <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                Weet je zeker dat je contactpersoon <strong id="modalContactName" class="text-slate-900"></strong> wilt loskoppelen van <strong id="modalVerkoperName" class="text-slate-900"></strong>?
            </p>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 mb-6 text-xs text-slate-500 leading-relaxed text-left">
                Let op: De contactpersoon blijft bewaard in het systeem, maar is niet langer gekoppeld aan deze verkoper.
            </div>

            <form id="loskoppelForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <input type="hidden" name="verkoper_id" id="modalVerkoperId" value="">

                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="closeLoskoppelModal()"
                        class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl transition"
                    >
                        Annuleren
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-sm rounded-xl shadow-sm transition"
                    >
                        Loskoppelen
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Algemene voettekst --}}
    @include('partials.footer')

    <script>
        function openLoskoppelModal(contactId, contactNaam, verkoperNaam, verkoperId) {
            document.getElementById('modalContactName').textContent = contactNaam;
            document.getElementById('modalVerkoperName').textContent = verkoperNaam;
            document.getElementById('modalVerkoperId').value = verkoperId;
            document.getElementById('loskoppelForm').action = '/contactpersonen/' + contactId + '/loskoppelen';
            document.getElementById('loskoppelModal').classList.remove('hidden');
        }

        function closeLoskoppelModal() {
            document.getElementById('loskoppelModal').classList.add('hidden');
        }
    </script>
</body>
</html>

