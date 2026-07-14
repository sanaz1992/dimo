<div x-data
    @scroll-to-customer.window="$nextTick(() => document.getElementById('order-customer-fields')?.scrollIntoView({ behavior: 'smooth', block: 'center' }))">
    <div id="order-customer-fields" class="mt-5">
        <div class="gap-4 grid grid-cols-1 md:grid-cols-3">
            <div class="flex flex-col gap-2">
                <label class="w-30 shrink-0 text-[13px] font-semibold text-gray-800">
                    @lang('order::attributes.code')
                </label>
                <input wire:model="form.code"
                    class="w-full h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="@lang('order::messages.enter_code')" />
                @error('form.code')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="w-30 shrink-0 text-[13px] font-semibold text-gray-800">
                    @lang('order::attributes.customer_name')
                </label>
                <input wire:model="form.customer_name"
                    class="w-full h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="@lang('order::messages.enter_customer_name')" />
                @error('form.customer_name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label class="w-30 shrink-0 text-[13px] font-semibold text-gray-800">
                    @lang('order::attributes.customer_mobile')
                </label>
                <input wire:model="form.customer_mobile"
                    class="w-full h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="@lang('order::messages.enter_customer_mobile')" />
                @error('form.customer_mobile')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    <div class="mt-6 border-t border-gray-100"></div>
    <!-- تیتر محصول -->
    <div class="mt-4">
        <h2 class="text-[15px] font-bold text-gray-900">
            @lang('order::messages.add_product_to_invoice')
        </h2>
    </div>

    <div class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-3">
        {{-- <div>
            <div class="mb-1 text-[12px] text-gray-500">
                @lang('order::attributes.product_category')
            </div>
            <div>
                <x-custom-select
                    :selected="collect($categories)->firstWhere('id', $form['category'] ?? null)?->title"
                    placeholder="{{ __('order::messages.choose') }}"
                    wire:key="category-select-{{ $form['category'] ?? 'empty' }}"
                >
                    <button type="button" x-on:click="choose('', @js(__('order::messages.choose')))" wire:click="changeCategory('')"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-600 transition hover:bg-gray-50 hover:text-gray-900">
                        <span>@lang('order::messages.choose')</span>
                    </button>
                    @foreach ($categories as $category)
                        <button type="button" x-on:click="choose(@js((string) $category->id), @js($category->title))" wire:click="changeCategory('{{ $category->id }}')"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                            <span>{{ $category->title }}</span>
                        </button>
                    @endforeach
                </x-custom-select>
                @error('form.category')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div> --}}

        @for ($i = 0; $i < $categoryDepth; $i++)

                <div>
                    <div class="mb-1 text-[12px] text-gray-500">
                        @lang('order::attributes.product_category') @lang('core::attributes.level') {{ $i+1 }}
                    </div>
                    <div>
                        <x-custom-select
                            {{-- :selected="collect($categories[$i]??[])->firstWhere('id', $form['category'] ?? null)?->title" --}}
                            :selected="$selectedText[$i]??''"
                            placeholder="{{ __('order::messages.choose') }}"
                            wire:key="category-select-{{ $form['category'] ?? 'empty' }}"
                        >
                         @if(isset($categories[$i])&&count($categories[$i]))
                            <button type="button" x-on:click="choose('', @js(__('order::messages.choose')))" wire:click="changeCategory('')"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-600 transition hover:bg-gray-50 hover:text-gray-900">
                                <span>@lang('order::messages.choose')</span>
                            </button>
                            @foreach ($categories[$i] as $category)
                                <button type="button" x-on:click="choose(@js((string) $category->id), @js($category->title))" wire:click="changeCategory('{{ $i }}','{{ $category->id }}')"
                                    class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                                    <span>{{ $category->title }}</span>
                                </button>
                            @endforeach
                             @endif
                        </x-custom-select>
                        @error('form.categories.'.$i)
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

        @endfor

        <div>
            <div class="mb-1 text-[12px] text-gray-500">
                @lang('order::messages.select_product')
            </div>
            <div>
                <x-custom-select
                    :selected="collect($products)->firstWhere('id', $form['product_id'] ?? null) ? collect($products)->firstWhere('id', $form['product_id'] ?? null)->title : ''"
                    placeholder="{{ __('order::messages.choose') }}"
                    wire:key="product-select-{{ $form['category'] ?? 'empty' }}-{{ $form['product_id'] ?? 'empty' }}"
                >
                    <div x-data="{
                        search: '',
                        products: [
                            @foreach($products as $pr)
                                { id: '{{$pr->id}}', title: @js(strtolower($pr->title)) },
                            @endforeach
                        ],
                        get hasResults() {
                            if (this.search === '') return true;
                            const s = this.search.toLowerCase();
                            return this.products.some(p => p.title.includes(s));
                        }
                    }" class="flex flex-col w-full relative">

                        <!-- Antigravity Search Input -->
                        <div class="sticky top-0 z-10 -mx-1 -mt-1.5 mb-1 bg-white/60 backdrop-blur-xl border-b border-gray-100/50 p-2 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)]">
                            <div class="relative group">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition-colors duration-300 group-focus-within:text-emerald-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" x-model="search" placeholder="جستجوی محصول..."
                                    class="w-full h-[40px] rounded-xl border border-white/40 bg-gray-50/50 pr-9 pl-3 text-[12px] font-semibold text-gray-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] outline-none transition-all duration-300 focus:bg-white focus:border-emerald-500/50 focus:shadow-[0_0_0_4px_rgba(16,185,129,0.1),_0_8px_16px_-4px_rgba(16,185,129,0.1)] focus:ring-0 placeholder:text-gray-400"
                                    @click.stop @keydown.stop />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1 p-1">
                            <button type="button" x-on:click="choose('', @js(__('order::messages.choose')))" wire:click="$set('form.product_id', '')"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-600 transition-all duration-300 hover:bg-gray-50 hover:text-gray-900"
                                x-show="search === ''">
                                <span>@lang('order::messages.choose')</span>
                            </button>

                            @foreach ($products as $pr)
                                <button type="button" x-on:click="choose(@js((string) $pr->id), @js($pr->title . ' - ' . $pr->price_number_format . ' ' . $currency))" wire:click="changeProduct('{{ $pr->id }}')"
                                    class="group flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2 text-right transition-all duration-300 hover:bg-emerald-50/60 hover:shadow-[0_4px_12px_-4px_rgba(16,185,129,0.15)] hover:-translate-y-[1px]"
                                    x-show="search === '' || @js(strtolower($pr->title)).includes(search.toLowerCase())">
                                    <div class="flex items-center gap-3">
                                        <div class="relative overflow-hidden rounded-lg shadow-[0_2px_8px_-2px_rgba(0,0,0,0.1)] transition-transform duration-300 group-hover:scale-105 group-hover:shadow-[0_4px_12px_-2px_rgba(16,185,129,0.2)]">
                                            <img src="{{ $pr->main_image->getThumbnailUrl() }}" class="w-8 h-8 object-cover border border-white/60" alt="{{ $pr->title }}" />
                                        </div>
                                        <span class="text-[12px] font-bold text-gray-700 transition-colors duration-300 group-hover:text-emerald-800">{{ $pr->title }}</span>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-gray-100/80 px-2.5 py-1 text-[11px] font-bold text-gray-500 shadow-sm transition-all duration-300 group-hover:bg-emerald-100 group-hover:text-emerald-800">
                                        {{ number_format($pr->effective_price) }} {{ $currency }}</span>
                                </button>
                            @endforeach

                            <!-- Empty State -->
                            <div x-show="!hasResults" style="display: none;"
                                class="flex flex-col items-center justify-center py-6 text-center transition-all duration-500"
                                x-transition:enter="transition ease-out duration-300 delay-100"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <div class="mb-3 rounded-full bg-gray-50 p-3 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]">
                                    <svg class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <span class="text-[12px] font-bold text-gray-500">محصولی یافت نشد</span>
                                <span class="mt-1 text-[11px] font-medium text-gray-400">نام دیگری را جستجو کنید</span>
                            </div>
                        </div>
                    </div>
                </x-custom-select>
                @error('form.product_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div>
            <label class="mb-1 text-[12px] text-gray-500">
                @lang('order::attributes.qty')
            </label>
            <input wire:model="form.qty" wire:key="qty-{{ now()->timestamp }}"
                class="w-full h-10 rounded-xl border border-gray-200 bg-white pr-9 pl-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                placeholder="@lang('order::messages.enter_qty')" />
            @error('form.qty')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        @if($hasFrame)
            <div>
                <div class="mb-1 text-[12px] text-gray-500">
                    @lang('order::messages.select_frame_color')
                </div>
                <div>
                    <x-custom-select
                        :selected="collect($frameColors)->firstWhere('id', $form['frame_color_id'] ?? null)?->title"
                        placeholder="{{ __('order::messages.choose') }}"
                        wire:key="frame-color-select-{{ $form['frame_color_id'] ?? 'empty' }}"
                    >
                        <button type="button" x-on:click="choose('', @js(__('order::messages.choose')))" wire:click="$set('form.frame_color_id', '')"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-600 transition hover:bg-gray-50 hover:text-gray-900">
                            <span>@lang('order::messages.choose')</span>
                        </button>
                        @foreach ($frameColors as $color)
                            <button type="button" x-on:click="choose(@js((string) $color->id), @js($color->title))" wire:click="$set('form.frame_color_id', '{{ $color->id }}')"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                                <span>{{ $color->title }}</span>
                            </button>
                        @endforeach
                    </x-custom-select>
                    @error('form.frame_color_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <!-- فریم اختصاصی -->
                <div class="flex flex-wrap items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 mt-8">
                        <input type="checkbox" wire:model="form.custom_frame"
                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                        فریم اختصاصی
                    </label>
                    <!-- <p class="text-[12px] text-gray-400"> -->
                </div>
            </div>
        @endif
    </div>
    {{-- {{!- Product Specifications Row - Frame material and dimensions -}} --}}
    @if($hasFabric || $form['send_fabric_by_customer'])
        <div class="mt-3  gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" wire:model="form.send_fabric_by_customer"
                            wire:change="changeSendFabricByCustomer($event.target.value)"
                            class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                        {{__('order::attributes.send_fabric_by_customer')}}
                    </label>
                </div>
            </div>
        </div>
        @if ($hasFabric)
            @foreach ($selectedProduct->required_fabrics as $requiredFabric)
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <div class="mb-1 text-[12px] text-gray-500">
                            مدل پارچه برای {{$requiredFabric->title}} را انتخاب کنید
                        </div>
                        <div>
                            <x-custom-select
                                :selected="collect($fabricModels)->firstWhere('id', $form['fabrics'][$requiredFabric->id]['fabric_model_id'] ?? null)?->title"
                                placeholder="{{ __('order::messages.choose') }}"
                                wire:key="fabric-model-select-{{ $requiredFabric->id }}-{{ $form['product_id'] ?? 'new' }}-{{ $form['fabrics'][$requiredFabric->id]['fabric_model_id'] ?? 'empty' }}"
                            >
                                <button type="button" x-on:click="choose('', @js(__('order::messages.choose')))" wire:click="$set('form.fabrics.{{ $requiredFabric->id }}.fabric_model_id', '')"
                                    class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-600 transition hover:bg-gray-50 hover:text-gray-900">
                                    <span>@lang('order::messages.choose')</span>
                                </button>
                                @foreach ($fabricModels as $fabricModel)
                                    <button type="button" x-on:click="choose(@js((string) $fabricModel->id), @js($fabricModel->title))" wire:click="changeFabricModel({{ $requiredFabric->id }}, '{{ $fabricModel->id }}')"
                                        class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                                        <span>{{ $fabricModel->title }}</span>
                                    </button>
                                @endforeach
                            </x-custom-select>
                            @error('form.fabrics.'.$requiredFabric->id.'.fabric_model_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 text-[12px] text-gray-500">
                            کد رنگ پارچه برای {{$requiredFabric->title}} را انتخاب کنید
                        </div>
                        <div>
                            @php
                                $selectedFabricModelId = $form['fabrics'][$requiredFabric->id]['fabric_model_id'] ?? null;
                                $selectedFabricId = $form['fabrics'][$requiredFabric->id]['fabric_id'] ?? null;
                                $fabricOptions = $selectedFabricModelId && isset($fabrics[$selectedFabricModelId]) ? $fabrics[$selectedFabricModelId] : collect();
                            @endphp
                            <x-custom-select
                                :selected="collect($fabricOptions)->firstWhere('id', $selectedFabricId)?->color_code"
                                placeholder="{{ __('order::messages.choose') }}"
                                wire:key="fabric-select-{{ $requiredFabric->id }}-{{ $form['product_id'] ?? 'new' }}-{{ $selectedFabricModelId ?? 'empty' }}-{{ $selectedFabricId ?? 'empty' }}"
                            >
                                <button type="button" x-on:click="choose('', @js(__('order::messages.choose')))" wire:click="$set('form.fabrics.{{ $requiredFabric->id }}.fabric_id', '')"
                                    class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-600 transition hover:bg-gray-50 hover:text-gray-900">
                                    <span>@lang('order::messages.choose')</span>
                                </button>
                                @if(isset($form['fabrics'][$requiredFabric->id]['fabric_model_id']))
                                    @foreach ($fabricOptions as $fabric)
                                        <button type="button" x-on:click="choose(@js((string) $fabric->id), @js($fabric->color_code))" wire:click="changeFabric({{ $requiredFabric->id }}, '{{ $fabric->id }}')"
                                            class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-right text-sm font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                                            {{-- <span>{{ $fabric->color_code }}</span> --}}
                                            <div class="flex items-center gap-3">
                                        <div class="relative overflow-hidden rounded-lg shadow-[0_2px_8px_-2px_rgba(0,0,0,0.1)] transition-transform duration-300 group-hover:scale-105 group-hover:shadow-[0_4px_12px_-2px_rgba(16,185,129,0.2)]">
                                            <img src="{{ $fabric->main_image->getThumbnailUrl() }}" class="w-8 h-8 object-cover border border-white/60" alt="{{ $fabric->color_code }}" />
                                        </div>
                                        <span class="text-[12px] font-bold text-gray-700 transition-colors duration-300 group-hover:text-emerald-800">{{ $fabric->color_code }}</span>
                                    </div>
                                        </button>
                                    @endforeach
                                @endif
                            </x-custom-select>
                            @error('form.fabrics.'.$requiredFabric->id.'.fabric_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
    <div class="mt-8 h-[1px] w-full bg-black/10"></div>

    <div class="py-4 flex justify-start mb-8">
        <x-Core::button wire:click="addProductToOrder"
            class="bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72]">
            @lang('order::attributes.add_product_to_order')
        </x-Core::button>
    </div>
</div>
