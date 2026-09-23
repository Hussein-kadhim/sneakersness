@php
    $isLoggedIn = false;
    $userName = 'Gebruiker';

    try {
        if (\Illuminate\Support\Facades\Auth::check()) {
            $isLoggedIn = true;
            $userName = \Illuminate\Support\Facades\Auth::user()?->name ?? 'Gebruiker';
        }
    } catch (\Throwable $e) {
        // Unhappy scenario (database down): vang database fouten netjes af
        // Controleer sessie keys zonder SQL query uit te voeren
        $sessionKeys = array_keys(session()->all());
        foreach ($sessionKeys as $key) {
            if (str_starts_with($key, 'login_web_')) {
                $isLoggedIn = true;
                break;
            }
        }
        $userName = 'Ingelogd';
    }
@endphp

<header class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <div class="overlay"></div>
    <div class="sn-header-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <div class="flex items-center gap-6 lg:gap-10">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-500 flex items-center justify-center font-bold text-white text-base">
                        SN
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 hidden sm:inline">
                        Sneakerness Rotterdam
                    </span>
                    <span class="text-base font-bold tracking-tight text-slate-900 sm:hidden">
                        Sneakerness RTM
                    </span>
                </a>

                <nav class="navbar hidden md:flex items-center gap-1.5 text-sm font-medium" aria-label="Hoofdnavigatie">
                    <button type="button" class="close-menu md:hidden text-xl text-slate-400 hover:text-slate-700 self-end mb-2" aria-label="Sluiten">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <a href="{{ route('home') }}" class="w-full md:w-auto px-3.5 py-1.5 {{ request()->routeIs('home') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-slate-500 hover:text-slate-900' }} rounded-lg">
                        Home
                    </a>
                    @if($isLoggedIn)
                        <a href="{{ route('tickets.index') }}" class="w-full md:w-auto px-3.5 py-1.5 {{ request()->routeIs('tickets.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-slate-500 hover:text-slate-900' }} rounded-lg">
                            Tickets
                        </a>
                        <a href="{{ route('verkopers.index') }}" class="w-full md:w-auto px-3.5 py-1.5 {{ (request()->routeIs('verkopers.*') && !request()->routeIs('home')) ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-slate-500 hover:text-slate-900' }} rounded-lg">
                            Verkopers
                        </a>
                        <a href="{{ route('contactpersonen.index') }}" class="w-full md:w-auto px-3.5 py-1.5 {{ request()->routeIs('contactpersonen.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-slate-500 hover:text-slate-900' }} rounded-lg">
                            Contactpersonen
                        </a>
                        <a href="{{ route('stands.index') }}" class="w-full md:w-auto px-3.5 py-1.5 {{ request()->routeIs('stands.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-slate-500 hover:text-slate-900' }} rounded-lg">
                            Stands
                        </a>
                        <a href="{{ route('events.index') }}" class="w-full md:w-auto px-3.5 py-1.5 {{ request()->routeIs('events.*') ? 'text-orange-600 bg-orange-50 font-semibold' : 'text-slate-500 hover:text-slate-900' }} rounded-lg">
                            Events
                        </a>
                    @endif

                    <div class="pt-3 mt-1.5 border-t border-slate-200 w-full flex flex-col gap-2 md:hidden">
                        @if($isLoggedIn)
                            <div class="px-3.5 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                {{ $userName }}
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left px-3.5 py-1.5 text-slate-500 hover:text-slate-900 rounded-lg">
                                    Uitloggen
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="w-full px-3.5 py-1.5 text-slate-700 hover:text-slate-900 font-semibold rounded-lg">
                                Inloggen
                            </a>
                            <a href="{{ route('register') }}" class="w-full px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg text-center">
                                Registreren
                            </a>
                        @endif
                    </div>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-3">
                    @if($isLoggedIn)
                        <span class="text-sm font-medium text-slate-700">{{ $userName }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-slate-500 hover:text-slate-900">
                                Uitloggen
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-900 hover:text-orange-500 px-3 py-2">
                            Inloggen
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg">
                            Registreren
                        </a>
                    @endif
                </div>

                <button type="button" class="hamburger md:hidden border border-slate-200 rounded-lg p-2 text-slate-700 hover:bg-slate-50 flex items-center justify-center" aria-label="Menu">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </div>
</header>
