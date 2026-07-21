@if ($paginator->hasPages())
    <div class="pagination">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" aria-disabled="true">‹</span>
        @else
            <button type="button" wire:click="previousPage" class="page-link" aria-label="صفحه قبلی">
                ‹
            </button>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-link disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link active">{{ $page }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $page }})" class="page-link">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button type="button" wire:click="nextPage" class="page-link" aria-label="صفحه بعدی">
                ›
            </button>
        @else
            <span class="page-link disabled" aria-disabled="true">›</span>
        @endif
    </div>
@endif
