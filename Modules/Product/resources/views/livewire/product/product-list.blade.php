<section>
    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="font-semibold text-lg md:text-xl">
                    {{__('product::attributes.product_list')}}
                </h2>
                <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                    <!-- منوی فیلتر -->
                    {{-- <x-Product::filters /> --}}
                    <livewire:product::product-advanced-filters :search="$search" :category="$category"
                        :price_min="$price_min" :price_max="$price_max" :published="$published" />
                    <!-- دسته بندی -->
                    {{-- <x-custom-select compact class="w-full sm:w-[200px]"
                        :selected="$allTabs[$activeTab] ?? 'همه محصولات'" placeholder="همه محصولات">
                        @foreach ($allTabs as $id => $title)
                        <button type="button" x-on:click="choose('{{ $id }}', '{{ $title }}')"
                            wire:click="setTab('{{ $id }}')"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-right text-xs font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                            <span>{{ $title }}</span>
                        </button>
                        @endforeach
                    </x-custom-select> --}}
                    <!-- منوی مرتب‌سازی -->
                    <x-custom-select compact class="w-full sm:w-[200px]" :selected="$sortOptions[$sortBy] ?? __('core::attributes.newest')" placeholder="{{ __('core::attributes.sort') }}">
                        @foreach ($sortOptions as $value => $label)
                            <button type="button" x-on:click="choose('{{ $value }}', '{{ $label }}')"
                                wire:click="changeSortBy('{{ $value }}')"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-right text-xs font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                                <span>{{ $label }}</span>
                            </button>
                        @endforeach
                    </x-custom-select>
                    @can('products_create')
                        <a href="{{route('admin.products.create')}}"
                            class="bg-[#3E3E3B] flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-white focus:outline-none font-bold whitespace-nowrap">
                            <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add"
                                class="w-5 flex-shrink-0" />
                            <span class="hidden sm:inline">@lang("product::attributes.product_create")</span>
                            <span class="sm:hidden">@lang("product::attributes.add")</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>


        <!-- محتوای تب‌ها -->
        <div class="tab-content">
            <!-- تب انبار اولیه -->
            <div class="relative">
                <div class="rounded-xl border border-black/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-[800px] w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    @foreach ($columns as $col)
                                        <th
                                            class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $col['label'] }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($products as $key => $product)
                                    <tr class="hover:bg-gray-50 {{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}">
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            <img alt="{{$product->title}}" class="h-10 w-10 rounded-full object-cover"
                                                src="{{ $product->main_image?->getThumbnailUrl('small') }}">
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            @can('products_edit')
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                    class="text-blue-500 hover:text-blue-700">{{$product->code}}</a>
                                            @else
                                                {{$product->code}}
                                            @endcan
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$product->title}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$product->category->title}}
                                        </td>
                                        {{-- <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$product->stock}}
                                        </td> --}}
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{ number_format($product->final_price) }}
                                        </td>
                                        <td>
                                            <span wire:click="publishProduct({{$product->id}})"
                                                class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{$product->status['class']}}">
                                                {{ $product->status['title'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            @can('products_edit')
                                                <a href="{{route('admin.products.edit', $product)}}"
                                                    class="block text-sm text-gray-700 hover:bg-gray-100 p-1"
                                                    style="float: right;">
                                                    <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/edit-2.svg') }}"
                                                        alt="add" class="w-5" />
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columns) }}"
                                            class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{__('core::messages.without_item')}}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$products->links('Core::pagination')}}
            </div>
        </div>
    </div>
</section>