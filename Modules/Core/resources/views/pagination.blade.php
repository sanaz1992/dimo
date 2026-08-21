@if ($paginator->hasPages())
    <nav role="navigation" aria-label="ناوبری صفحه‌بندی"
        class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

        {{-- اطلاعات pagination --}}
        <div class="text-[12px] text-ink-faint">
            نمایش

            @if ($paginator->firstItem())
                <span class="font-semibold text-ink">
                    {{ $paginator->firstItem() }}
                </span>

                تا

                <span class="font-semibold text-ink">
                    {{ $paginator->lastItem() }}
                </span>
            @else
                <span class="font-semibold text-ink">
                    {{ $paginator->count() }}
                </span>
            @endif

            از

            <span class="font-semibold text-ink">
                {{ $paginator->total() }}
            </span>

            نتیجه
        </div>


        {{-- pagination --}}
        <div class="flex items-center gap-1">

            {{-- صفحه قبل --}}
            <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
                @disabled($paginator->onFirstPage()) class="row-btn disabled:cursor-not-allowed disabled:opacity-40"
                aria-label="صفحه قبل">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>


            {{-- شماره صفحات --}}
            @foreach ($elements as $element)

                {{-- ... --}}
                @if (is_string($element))
                    <span
                        class="flex h-9 min-w-9 items-center justify-center rounded-xl px-2 text-[12px] font-semibold text-ink-faint">
                        {{ $element }}
                    </span>
                @endif


                {{-- صفحات --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)

                        <button type="button" wire:click="gotoPage({{ $page }})" class="flex h-9 min-w-9 items-center justify-center rounded-xl px-2 text-[12px] font-semibold transition
                                                {{ $page == $paginator->currentPage()
                                    ? 'bg-blue-600 text-white'
                                    : 'text-ink-faint hover:bg-slate-100 hover:text-ink'
                                                }}">
                            {{ $page }}
                        </button>

                    @endforeach
                @endif

            @endforeach


            {{-- صفحه بعد --}}
            <button type="button" wire:click="nextPage" wire:loading.attr="disabled" @disabled(!$paginator->hasMorePages())
                class="row-btn disabled:cursor-not-allowed disabled:opacity-40" aria-label="صفحه بعد">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>

            </button>

        </div>

    </nav>
@endif
