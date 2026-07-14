@if ($paginator->hasPages())
    <nav role="navigation" aria-label="ناوبری صفحه‌بندی" class="flex items-center justify-between rtl mt-2">
        {{-- Mobile --}}
        <div class="flex justify-between flex-1 sm:hidden">
            <button
                type="button"
                wire:click="previousPage"
                @disabled($paginator->onFirstPage())
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium border rounded-md
                    {{ $paginator->onFirstPage()
                        ? 'text-gray-500 bg-white border-gray-300 cursor-default'
                        : 'text-gray-700 bg-white border-gray-300 hover:text-gray-500' }}">
                قبلی
            </button>

            <button
                type="button"
                wire:click="nextPage"
                @disabled(! $paginator->hasMorePages())
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium border rounded-md
                    {{ ! $paginator->hasMorePages()
                        ? 'text-gray-500 bg-white border-gray-300 cursor-default'
                        : 'text-gray-700 bg-white border-gray-300 hover:text-gray-500' }}">
                بعدی
            </button>
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    نمایش
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        تا
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    از
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    نتیجه
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                    {{-- Previous --}}
                    <button
                        type="button"
                        wire:click="previousPage"
                        @disabled($paginator->onFirstPage())
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium border rounded-l-md
                            {{ $paginator->onFirstPage()
                                ? 'text-gray-500 bg-white border-gray-300 cursor-default'
                                : 'text-gray-500 bg-white border-gray-300 hover:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    {{-- Pages --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default">
                                {{ $element }}
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $page }})"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium border
                                        {{ $page == $paginator->currentPage()
                                            ? 'text-gray-500 bg-white border-gray-300 cursor-default'
                                            : 'text-gray-700 bg-white border-gray-300 hover:text-gray-500' }}">
                                    {{ $page }}
                                </button>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    <button
                        type="button"
                        wire:click="nextPage"
                        @disabled(! $paginator->hasMorePages())
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium border rounded-r-md
                            {{ ! $paginator->hasMorePages()
                                ? 'text-gray-500 bg-white border-gray-300 cursor-default'
                                : 'text-gray-500 bg-white border-gray-300 hover:text-gray-400' }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </span>
            </div>
        </div>
    </nav>
@endif
