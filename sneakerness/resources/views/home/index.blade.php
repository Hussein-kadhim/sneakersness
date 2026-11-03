{{-- Landingspagina van Sneakerness Rotterdam --}}
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sneakerness Rotterdam | Home</title>
    <meta name="description" content="Het centrale overzicht van Sneakerness Rotterdam 2026.">
    {{-- Lettertypes en stijlen inladen --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/verkopers/verkopersoverzicht.css', 'resources/css/home/home.css', 'resources/js/app.js'])
</head>
<body class="home-page" x-data="{ showBuyModal: false }">
    {{-- Bovenste navigatiebalk --}}
    @include('partials.header')

    <main>
        {{-- Succesmelding na actie (happy scenario) --}}
        @if(session('success'))
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div id="success-alert" class="p-4 bg-emerald-600 text-white rounded-xl font-medium text-sm sm:text-base flex items-center gap-3 shadow-sm transition-opacity duration-500" role="status">
                    <i class="fa-solid fa-check text-white text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    var alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.style.opacity = '0';
                        setTimeout(function() { alert.remove(); }, 500);
                    }
                }, 4000);
            </script>
        @endif

        {{-- Foutmelding na aankoop / storing (unhappy scenario) --}}
        @if(session('error'))
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mt-6">
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-sm" role="alert">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-500 shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-red-900 text-sm sm:text-base">
                            {{ str_contains(session('error'), 'kopen') ? 'Probleem bij aankoop' : 'Foutmelding' }}
                        </h2>
                        <p class="text-red-700 text-xs sm:text-sm mt-0.5 leading-relaxed">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Hero sectie met introductie en actieknoppen --}}
        <section class="hero-shell">
            <div class="hero-copy">
                <p class="eyebrow">ROTTERDAM / 2026 / COMMUNITY</p>
                <h1>THE CULTURE<br><span>IN MOTION.</span></h1>
                <p class="hero-text">Sneakerness brengt verzamelaars, makers en de nieuwste heat samen onder één dak.</p>
                <div class="hero-actions">
                    <a href="{{ route('verkopers.index') }}" class="button button-dark">Ontdek verkopers <i class="fa-solid fa-arrow-right"></i></a>
                    <button type="button" @click="showBuyModal = true" class="button button-outline" style="cursor: pointer;" id="btn_ticket_kopen">
                        <i class="fa-solid fa-cart-shopping"></i> Ticket Kopen
                    </button>
                </div>
            </div>
            <div class="hero-art" aria-label="Sneakerness Rotterdam editie 2026">
                <div class="art-stamp">ROTTERDAM<br><strong>2026</strong></div>
                <div class="art-scribble">STEP<br>INTO<br>THE<br>HEAT</div>
                <div class="art-shoe"><i class="fa-solid fa-shoe-prints"></i></div>
                <div class="art-label">EST. 2012 <span>✳</span> ALWAYS MOVING</div>
            </div>
        </section>

        {{-- Statistiekenbalk met actuele cijfers --}}
        <section class="stats-row" aria-label="Sneakerness cijfers">
            <div><strong>{{ $totalVerkopers }}</strong><span>verkopers</span></div>
            <div><strong>{{ $partnerCount }}</strong><span>partners</span></div>
            <div><strong>{{ $rentedStandsCount }}<small>/{{ $totalStands }}</small></strong><span>stands gevuld</span></div>
            <div class="stats-note"><span>01</span><strong>ONE COMMUNITY.<br>ENDLESS STORIES.</strong></div>
        </section>

        {{-- Uitgelichte verkopers (spotlight selectie) --}}
        <section class="content-section" id="programma">
            <div class="section-heading">
                <div><p class="eyebrow">DISCOVER THE LINE-UP</p><h2>In the spotlight</h2></div>
                <a href="{{ route('verkopers.index') }}" class="arrow-link">Alle verkopers <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </div>
            <div class="seller-grid">
                @forelse($verkopers as $index => $verkoper)
                    <a href="{{ route('verkopers.index') }}" class="seller-card" aria-label="Bekijk {{ $verkoper->Naam }} bij de verkopers">
                        <span class="card-number">0{{ $index + 1 }}</span>
                        <div class="seller-icon"><i class="fa-solid fa-star"></i></div>
                        <div class="seller-info">
                            <p>{{ $verkoper->VerkooptSoort ?: 'Sneaker culture' }}</p>
                            <h3>{{ $verkoper->Naam }}</h3>
                        </div>
                        @if($verkoper->SpecialeStatus)
                            <span class="partner-tag">PARTNER</span>
                        @endif
                        <i class="fa-solid fa-arrow-up-right-from-square card-arrow"></i>
                    </a>
                @empty
                    <div class="empty-state">Binnenkort ontdek je hier onze verkopers.</div>
                @endforelse
            </div>
        </section>

        {{-- Overzicht van populairste productcategorieën --}}
        <section class="category-section">
            <div class="category-intro"><p class="eyebrow">FIND YOUR THING</p><h2>Alles wat<br>beweegt.</h2></div>
            <div class="category-list">
                @forelse($categories as $category)
                    <a href="{{ route('verkopers.index', ['category' => $category->name]) }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $category->name }}</strong><em>{{ $category->total }} verkopers</em><i class="fa-solid fa-arrow-right"></i></a>
                @empty
                    <p>Nog geen categorieën beschikbaar.</p>
                @endforelse
            </div>
        </section>
    </main>

    {{-- Voettekst --}}
    <footer class="home-footer"><span>SNEAKERNESS <b>ROTTERDAM</b></span><span>MADE FOR THE CULTURE <i>✳</i></span><span>© {{ date('Y') }}</span></footer>

    <!-- Ticket Kopen Modal -->
    <div
        x-show="showBuyModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-buy-title"
        role="dialog"
        aria-modal="true"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div
            x-show="showBuyModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            @click="showBuyModal = false"
        ></div>

        <!-- Modal Dialog -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                x-show="showBuyModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
                @click.stop
            >
                @if(!isset($events) || $events->isEmpty())
                    <div class="bg-white px-6 pt-6 pb-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-3 text-lg">
                            <i class="fa-solid fa-ticket-simple"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1" id="modal-buy-title">Geen tickets beschikbaar</h3>
                        <p class="text-sm text-slate-600 mb-6">
                            Er bestaan op dit moment geen tickets om te kopen. Probeer het later opnieuw.
                        </p>
                        <button
                            type="button"
                            @click="showBuyModal = false"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl cursor-pointer"
                        >
                            Sluiten
                        </button>
                    </div>
                @else
                    <form method="POST" action="{{ route('tickets.kopen') }}">
                        @csrf
                        <input type="hidden" name="redirect_to" value="home">
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-sm font-bold">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900" id="modal-buy-title">Ticket Kopen</h3>
                                        <p class="text-xs text-slate-500">Selecteer een ticket en reken direct af</p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="showBuyModal = false"
                                    class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 hover:bg-slate-100 cursor-pointer"
                                >
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="modal_ticket_selectie" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Selecteer Ticket / Evenement
                                    </label>
                                    <select
                                        name="ticket_selectie"
                                        id="modal_ticket_selectie"
                                        required
                                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium"
                                    >
                                        <option value="">-- Kies een ticket om te kopen --</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->Id }}" {{ $loop->first ? 'selected' : '' }}>
                                                {{ $event->Naam }} &bull; Regulier ticket &bull; &euro; 17,50
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="modal_aantal_tickets" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Aantal tickets
                                    </label>
                                    <input
                                        type="number"
                                        name="aantal_tickets"
                                        id="modal_aantal_tickets"
                                        value="1"
                                        min="1"
                                        max="10"
                                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button
                                type="button"
                                @click="showBuyModal = false"
                                class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg shadow-sm cursor-pointer"
                            >
                                Annuleren
                            </button>
                            <button
                                type="submit"
                                id="btn_aankoop_bevestigen"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors cursor-pointer"
                            >
                                <i class="fa-solid fa-cart-shopping text-xs"></i>
                                Aankoop bevestigen
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>