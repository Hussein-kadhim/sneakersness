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
@include('partials.header')

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