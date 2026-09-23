@if ($paginator->hasPages())
    <div class="flex items-center gap-1 sm:gap-1.5 text-xs">
        @if ($paginator->onFirstPage())
            <span class="px-2.5 py-1 border border-slate-200 rounded text-slate-300">
                Vorige
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-2.5 py-1 border border-slate-200 rounded text-slate-600 hover:bg-slate-50">
                Vorige
            </a>
        @endif

        <div class="hidden sm:flex items-center gap-1 sm:gap-1.5">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1 text-slate-400">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-2.5 py-1 bg-orange-500 text-white font-bold rounded">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-2.5 py-1 border border-slate-200 rounded text-slate-600 hover:bg-slate-50">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <span class="sm:hidden px-2 py-1 font-semibold text-slate-700">
            {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-2.5 py-1 border border-slate-200 rounded text-slate-600 hover:bg-slate-50">
                Volgende
            </a>
        @else
            <span class="px-2.5 py-1 border border-slate-200 rounded text-slate-300">
                Volgende
            </span>
        @endif
    </div>
@endif
