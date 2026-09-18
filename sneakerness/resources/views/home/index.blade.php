<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sneakerness Rotterdam | Home</title>
    <meta name="description" content="Het centrale overzicht van Sneakerness Rotterdam 2026.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/verkopers/verkopersoverzicht.css', 'resources/css/home/home.css', 'resources/js/app.js'])
</head>
<body class="home-page">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="overlay"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-500 flex items-center justify-center font-bold text-white text-base">SN</div>
                        <span class="text-xl font-bold tracking-tight text-slate-900 hidden sm:inline">Sneakerness Rotterdam</span>
                        <span class="text-base font-bold tracking-tight text-slate-900 sm:hidden">Sneakerness RTM</span>
                    </a>
                </div>

                <nav class="navbar hidden md:flex items-center gap-1.5 text-sm font-medium" aria-label="Hoofdnavigatie">
                    <button type="button" class="close-menu md:hidden text-xl text-slate-400 hover:text-slate-700 self-end mb-2" aria-label="Sluiten"><i class="fa-solid fa-xmark"></i></button>
                    <a href="{{ route('home') }}" class="w-full md:w-auto px-3.5 py-1.5 text-orange-600 bg-orange-50 font-semibold rounded-lg">Home</a>
                    <a href="{{ route('verkopers.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Verkopers</a>
                    <a href="{{ route('contactpersonen.index') }}" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Contactpersonen</a>
                    <a href="#programma" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Stand huren</a>
                    <a href="#programma" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Events</a>
                    <a href="#programma" class="w-full md:w-auto px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Bezoekers</a>

                    <div class="pt-3 mt-1.5 border-t border-slate-200 w-full flex flex-col gap-2 md:hidden">
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="w-full">@csrf<button type="submit" class="w-full text-left px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">Uitloggen</button></form>
                        @else
                            <a href="{{ route('login') }}" class="w-full px-3.5 py-1.5 text-slate-700 hover:text-slate-900 font-semibold rounded-lg">Inloggen</a>
                            <a href="{{ route('register') }}" class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg text-center">Registreren</a>
                        @endauth
                    </div>
                </nav>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-3">
                        @auth
                            <span class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-sm text-slate-500 hover:text-slate-900">Uitloggen</button></form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-900 hover:text-orange-500 px-3 py-2">Inloggen</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg">Registreren</a>
                        @endauth
                    </div>
                    <button type="button" class="hamburger md:hidden border border-slate-200 rounded-lg p-2 text-slate-700 hover:bg-slate-50 flex items-center justify-center" aria-label="Menu"><i class="fa-solid fa-bars text-lg"></i></button>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-shell">
            <div class="hero-copy">
                <p class="eyebrow">ROTTERDAM / 2026 / COMMUNITY</p>
                <h1>THE CULTURE<br><span>IN MOTION.</span></h1>
                <p class="hero-text">Sneakerness brengt verzamelaars, makers en de nieuwste heat samen onder één dak.</p>
                <div class="hero-actions">
                    <a href="{{ route('verkopers.index') }}" class="button button-dark">Ontdek verkopers <i class="fa-solid fa-arrow-right"></i></a>
                    <a href="#programma" class="button button-outline">Bekijk programma</a>
                </div>
            </div>
            <div class="hero-art" aria-label="Sneakerness Rotterdam editie 2026">
                <div class="art-stamp">ROTTERDAM<br><strong>2026</strong></div>
                <div class="art-scribble">STEP<br>INTO<br>THE<br>HEAT</div>
                <div class="art-shoe"><i class="fa-solid fa-shoe-prints"></i></div>
                <div class="art-label">EST. 2012 <span>✳</span> ALWAYS MOVING</div>
            </div>
        </section>

        <section class="stats-row" aria-label="Sneakerness cijfers">
            <div><strong>{{ $totalVerkopers }}</strong><span>verkopers</span></div>
            <div><strong>{{ $partnerCount }}</strong><span>partners</span></div>
            <div><strong>{{ $rentedStandsCount }}<small>/{{ $totalStands }}</small></strong><span>stands gevuld</span></div>
            <div class="stats-note"><span>01</span><strong>ONE COMMUNITY.<br>ENDLESS STORIES.</strong></div>
        </section>

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

    <footer class="home-footer"><span>SNEAKERNESS <b>ROTTERDAM</b></span><span>MADE FOR THE CULTURE <i>✳</i></span><span>© {{ date('Y') }}</span></footer>
</body>
</html>