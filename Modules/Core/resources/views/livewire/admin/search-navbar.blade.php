<div class="relative w-52 md:w-96" {{-- x-data="{ open: false }" @click.away="open = false" --}}>

    <!-- Search Input -->
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-5">
        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
        </svg>
    </div>

    <input type="search" wire:input="changeQuery($event.target.value)" {{-- @focus="open = true" @input="open = true"
        --}} placeholder="در انبار جستجو کنید"
        class="block w-full rounded-full bg-[#F7F8F8] py-3 pl-4 pr-12 border-none text-xs md:text-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#3E3E3B] focus:border-transparent transition font-lahzeh-medium text-right" />

    @if($showSearchBox)
        <!-- Results -->
        <div class="absolute z-10 mt-1 w-full bg-white rounded-lg shadow-lg max-h-96 overflow-y-auto border border-gray-200"
            x-transition>

            <div class="divide-y divide-gray-100">
                @foreach ($products as $product)
                    <a href="{{ route('admin.products.edit', $product) }}"
                        class="flex items-center justify-between p-3 hover:bg-gray-50 transition-colors">

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $product->title }}</p>
                            <div class="flex items-center mt-1 text-xs text-gray-500">
                                <span>{{ $product->category?->title }}</span>
                                <span class="mx-1">•</span>
                                <span>کد:  {{ $product->code }}</span>
                            </div>
                        </div>

                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                            مشاهده
                        </span>
                    </a>
                @endforeach
            </div>
            @if(count($products))
                <!-- Footer with view all results link -->
                <div class="border-t border-gray-100 bg-gray-50 px-4 py-2 text-center">
                    <a href="{{ route('admin.products.index', ['search' => $search]) }}"
                        class="text-xs font-medium text-blue-600 hover:text-blue-800">
                        مشاهده همه نتایج ({{count($products)}})
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
