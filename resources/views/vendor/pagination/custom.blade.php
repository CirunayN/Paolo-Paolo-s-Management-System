@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3">
    <!-- Results Counter -->
    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">
        <span>Showing</span>
        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $paginator->firstItem() ?? 0 }}</span>
        <span>to</span>
        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $paginator->lastItem() ?? 0 }}</span>
        <span>of</span>
        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $paginator->total() }}</span>
        <span>results</span>
    </div>

    <!-- Page Number Navigation Buttons -->
    <div class="flex items-center gap-1.5 flex-wrap">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 rounded-xl text-xs font-bold text-slate-400 dark:text-slate-600 bg-slate-100 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 cursor-not-allowed select-none flex items-center gap-1.5">
                <i class="fas fa-chevron-left text-[10px]"></i> Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-dark-850 hover:bg-slate-100 dark:hover:bg-dark-750 border border-slate-300 dark:border-slate-700 hover:border-cyan-500 transition-all shadow-sm flex items-center gap-1.5">
                <i class="fas fa-chevron-left text-[10px]"></i> Previous
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="w-9 h-9 flex items-center justify-center text-xs font-bold text-slate-400 select-none">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-9 h-9 rounded-xl text-xs font-black bg-cyan-600 text-white flex items-center justify-center shadow-md shadow-cyan-600/30 border border-cyan-500 select-none">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-9 h-9 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-dark-850 hover:bg-slate-100 dark:hover:bg-dark-750 border border-slate-300 dark:border-slate-700 hover:border-cyan-500 hover:text-cyan-600 dark:hover:text-cyan-400 flex items-center justify-center transition-all shadow-sm">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-dark-850 hover:bg-slate-100 dark:hover:bg-dark-750 border border-slate-300 dark:border-slate-700 hover:border-cyan-500 transition-all shadow-sm flex items-center gap-1.5">
                Next <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        @else
            <span class="px-3 py-2 rounded-xl text-xs font-bold text-slate-400 dark:text-slate-600 bg-slate-100 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 cursor-not-allowed select-none flex items-center gap-1.5">
                Next <i class="fas fa-chevron-right text-[10px]"></i>
            </span>
        @endif
    </div>
</nav>
@endif
