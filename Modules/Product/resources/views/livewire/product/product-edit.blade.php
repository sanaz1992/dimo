@push('style')
    <style>
        /* Custom select styles */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: none;
            padding-right: 0.75rem !important;
            /* Reduced from default */
        }

        /* For IE11 */
        select::-ms-expand {
            display: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

<div class="container mx-auto px-4 rtl">
    <!-- Main product wizard container with Alpine.js component -->
    <div class="w-full">
        <!-- Progress indicator navigation showing 3 steps -->
        <x-Product::product-nav :step="$step" :max-step="$maxStep" />

        {{-- Cost Breakdown Widget --}}
        <section x-data="{ open: false }" class="rounded-xl border border-gray-200 bg-white shadow-sm mb-4">
            <button @click="open = !open" type="button"
                class="w-full flex items-center justify-between p-4 sm:p-5 text-right hover:bg-gray-50 transition-colors rounded-xl">
                <div class="flex items-center gap-3">
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">@lang('product::attributes.cost_breakdown')</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            @lang('product::attributes.grand_total'):
                            <span class="font-bold text-emerald-700">{{ number_format($grandTotalPrice ?? 0) }}</span>
                            {{ $currency }}
                        </p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-collapse x-cloak class="px-4 sm:px-5 pb-5">
                <div class="border-t border-gray-100 pt-4 space-y-5">

                    {{-- 1. Raw Materials --}}
                    @if($autoCalculateProductCost && $product->materials->count())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                @lang('product::attributes.raw_materials_cost')
                            </h4>
                            <div class="rounded-xl overflow-hidden border border-gray-100">
                                <table class="w-full text-sm text-gray-700">
                                    <thead>
                                        <tr class="text-center bg-amber-50/60">
                                            <th class="px-3 py-2 font-medium">#</th>
                                            <th class="px-3 py-2 font-medium">@lang('warehouse::attributes.code')</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.material_name')
                                            </th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.required_quantity')
                                            </th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.unit_price')
                                                ({{ $currency }})</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.total_price')
                                                ({{ $currency }})</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($product->materials as $material)
                                            <tr class="text-center">
                                                <td class="px-3 py-2 text-gray-400">{{ $loop->iteration }}</td>
                                                <td class="px-3 py-2">
                                                    @can('warehouse_items_edit')
                                                        <a class="text-blue-500 hover:text-blue-700"
                                                            href="{{route('admin.warehouses.edit', $material)}}">
                                                            {{ $material->code }}
                                                        </a>
                                                    @else
                                                        {{ $material->code }}
                                                    @endcan
                                                </td>
                                                <td class="px-3 py-2">{{ $material->title }}</td>
                                                <td class="px-3 py-2">{{ $material->pivot->qty }}
                                                    @if($material->unit)
                                                        <span
                                                            class="text-xs text-gray-400">{{ $material->unit?->name ?? '' }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">{{ number_format($material->price) }}</td>
                                                <td class="px-3 py-2 font-medium">
                                                    {{ number_format($material->price * $material->pivot->qty) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-amber-50/40">
                                            <td colspan="5" class="px-3 py-2 text-left font-semibold text-gray-600">
                                                @lang('product::attributes.final_amount')
                                            </td>
                                            <td class="px-3 py-2 text-center font-bold text-amber-700">
                                                {{ number_format($materialTotalPrice ?? 0) }} {{ $currency }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- 2. Intermediate Products --}}
                    @if($autoCalculateProductCost && $product->intermediate_products->count())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                @lang('product::attributes.intermediate_products_cost')
                            </h4>
                            <div class="rounded-xl overflow-hidden border border-gray-100">
                                <table class="w-full text-sm text-gray-700">
                                    <thead>
                                        <tr class="text-center bg-blue-50/60">
                                            <th class="px-3 py-2 font-medium">#</th>
                                            <th class="px-3 py-2 font-medium">@lang('warehouse::attributes.code')</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.product_name')</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.count')</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.unit_price')
                                                ({{ $currency }})</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.total_price')
                                                ({{ $currency }})</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($product->intermediate_products as $ip)
                                            <tr class="text-center">
                                                <td class="px-3 py-2 text-gray-400">{{ $loop->iteration }}</td>
                                                <td class="px-3 py-2">
                                                    @can('products_edit')
                                                        <a class="text-blue-500 hover:text-blue-700"
                                                            href="{{route('admin.products.edit', $product)}}">
                                                            {{ $ip->code }}
                                                        </a>
                                                    @else
                                                        {{ $ip->code }}
                                                    @endcan
                                                </td>
                                                <td class="px-3 py-2">{{ $ip->title }}</td>
                                                <td class="px-3 py-2">{{ $ip->pivot->qty }}</td>
                                                <td class="px-3 py-2">{{ number_format($ip->final_price) }}</td>
                                                <td class="px-3 py-2 font-medium">
                                                    {{ number_format($ip->final_price * $ip->pivot->qty) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-blue-50/40">
                                            <td colspan="5" class="px-3 py-2 text-left font-semibold text-gray-600">
                                                @lang('product::attributes.final_amount')
                                            </td>
                                            <td class="px-3 py-2 text-center font-bold text-blue-700">
                                                {{ number_format($intermediateProductmaterialTotalPrice ?? 0) }}
                                                {{ $currency }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- 3. Cost Items (from product relationship - matches final_price calculation) --}}
                    @if($autoCalculateProductCost && $product->cost_items->count())
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                @lang('product::attributes.cost_items_cost')
                            </h4>
                            <div class="rounded-xl overflow-hidden border border-gray-100">
                                <table class="w-full text-sm text-gray-700">
                                    <thead>
                                        <tr class="text-center bg-purple-50/60">
                                            <th class="px-3 py-2 font-medium">#</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.title')</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.base_price')
                                                ({{ $currency }})</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.multiplier')</th>
                                            <th class="px-3 py-2 font-medium">@lang('product::attributes.total_price')
                                                ({{ $currency }})</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($product->cost_items as $ci)
                                            @if($ci->pivot->amount > 0)
                                                <tr class="text-center">
                                                    <td class="px-3 py-2 text-gray-400">{{ $loop->iteration }}</td>
                                                    <td class="px-3 py-2">{{ $ci->title }}</td>
                                                    <td class="px-3 py-2">{{ number_format($ci->base_price) }}</td>
                                                    <td class="px-3 py-2">{{ $ci->pivot->amount }}</td>
                                                    <td class="px-3 py-2 font-medium">
                                                        {{ number_format($ci->base_price * $ci->pivot->amount) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-purple-50/40">
                                            <td colspan="4" class="px-3 py-2 text-left font-semibold text-gray-600">
                                                @lang('product::attributes.final_amount')
                                            </td>
                                            <td class="px-3 py-2 text-center font-bold text-purple-700">
                                                {{ number_format($costItemsTotalPrice ?? 0) }} {{ $currency }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Manual Base Price (when auto-calculate is OFF) --}}
                    @if(!$autoCalculateProductCost)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                @lang('product::attributes.manual_base_price')
                            </h4>
                            <div class="rounded-xl bg-gray-50 border border-gray-200 p-3 flex items-center justify-between">
                                <span class="text-sm text-gray-600">@lang('product::attributes.price')</span>
                                <span class="font-bold text-gray-700">{{ number_format($product->price) }}
                                    {{ $currency }}</span>
                            </div>
                            <div class="mt-2 rounded-xl bg-amber-50 border border-amber-200 p-3 flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 text-amber-500 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-sm text-amber-700 leading-6">
                                    @lang('product::attributes.auto_calculate_off_note')
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Grand Total --}}
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-200 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="font-bold text-gray-800">@lang('product::attributes.grand_total')</span>
                            </div>
                            <span class="text-lg font-extrabold text-emerald-700">
                                {{ number_format($grandTotalPrice ?? 0) }} {{ $currency }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">

            {{-- step 1 --}}
            @if($step === 1)
                <div>
                    <h2 class="mb-4 text-xl font-bold">
                        @lang('product::attributes.product_edit')
                        @if($type == "intermediate")
                            @lang('product::attributes.intermediate')
                        @endif
                    </h2>

                    <div class="flex flex-col gap-4">
                        {{-- <livewire:media.image-upload-input wire:model="form.image" name="image"
                            :initial-image="$image" /> --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">
                                    @lang('product::attributes.image')
                                </label>
                                <p class="text-xs text-gray-500 mb-2">
                                    @lang('media::attributes.image_formats'):
                                    {{config('media.validations.image.mimes')}}
                                    (@lang('media::attributes.max')
                                    {{config('media.validations.image.max') / 1024}}
                                    @lang('media::attributes.MB'))
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="w-full flex-1">
                                    <label
                                        class="flex flex-col items-center justify-center w-full h-40 border rounded-3xl cursor-pointer hover:bg-gray-100 transition-colors border-[#D3E0E4] relative overflow-hidden
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{  $form['image'] || $initialImage ? 'border-green-500 bg-green-50' : '' }}">
                                        @if ($form['image'] || $initialImage)
                                            <div class="w-full h-full flex items-center justify-center p-2 text-center">
                                                <div>
                                                    <svg class="mx-auto h-10 w-10 text-green-500 mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7">
                                                        </path>
                                                    </svg>
                                                    <p class="text-sm text-gray-600">
                                                        @lang('media::attributes.image_selected')
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        @lang('media::messages.click_again_for_change_image')
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                    </path>
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500">
                                                    <span class="font-semibold">
                                                        @lang('media::messages.click_for_upload')
                                                    </span>
                                                    @lang('media::messages.or_drop_image')
                                                </p>
                                            </div>
                                        @endif
                                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            accept="image/jpeg, image/png, image/jpg" wire:model="form.image" />
                                    </label>
                                </div>

                                @if ($form['image'] || $initialImage)
                                    <div class="w-[200px] flex-shrink-0">
                                        <div
                                            class="h-40 w-full rounded-3xl overflow-hidden border border-[#D3E0E4] relative group">
                                            <img src="{{ $this->imagePreview }}" alt="@lang('media::attributes.image_preview')"
                                                class="h-full w-full object-cover" />
                                            <div
                                                class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 text-white text-xs p-1.5 text-center truncate">
                                                {{ $this->clientOriginalName }}
                                            </div>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <button type="button" wire:click="removeImage"
                                                    class="p-2 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity transform hover:scale-110"
                                                    title="@lang('media::attributes.delete_image')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0A1 1 0 019 6h6a1 1 0 011 1m-8 0h10" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @error('form.image')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-2 block text-sm font-medium">@lang('product::attributes.title')</label>
                                    <input wire:model="form.title" type="text" class="w-full rounded-lg border-gray-300"
                                        placeholder="@lang('product::messages.enter_title')" />
                                    @error('form.title')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium">@lang('product::attributes.code')</label>
                                    <input wire:model="form.code" type="text" class="w-full rounded-lg border-gray-300"
                                        placeholder="@lang('product::messages.enter_code')" />
                                    @error('form.code')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                @for ($i = 0; $i < $categoryDepth; $i++)
                                    <div class="mb-4">
                                        <label class="mb-2 block text-sm font-medium">
                                            @lang('category::attributes.category')
                                            @if($categoryDepth > 1)
                                                @lang('category::attributes.level') {{ $i + 1 }}
                                            @endif
                                        </label>

                                        <select wire:model.live="parents.{{ $i }}"
                                            class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300">
                                            <option value="">
                                                @lang('category::messages.choose')
                                            </option>
                                            @if(!empty($categories[$i]))
                                                @foreach ($categories[$i] as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->title }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                @endfor

                                @if(!$autoCalculateProductCost)
                                    <div>
                                        <label class="mb-2 block text-sm font-medium">
                                            @lang('product::attributes.price')
                                            ({{$currency}})
                                        </label>
                                        <div>
                                            <input type="text" class="w-full rounded-lg border-gray-300"
                                                placeholder="{{ __('product::messages.enter_price') }}"
                                                oninput="formatNumberInput(this, 'form.price')"
                                                value="{{ number_format((float) ($form['price'] ?? 0)) }}" />

                                            <input type="hidden" wire:model.defer="form.price" />
                                        </div>
                                        @error('form.price')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if($enableProductBaseSales)
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium">@lang('product::attributes.product_order_type')</label>
                                        <select wire:model="form.order_type" wire:change="changeOrderType($event.target.value)"
                                            class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300">
                                            <option value="">@lang('product::messages.choose')</option>
                                            @foreach (\Modules\Product\Enums\ProductOrderType::cases() as $orderType)
                                                <option value="{{ $orderType->value}}"> {{ $orderType->label() }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.order_type')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium">@lang('product::attributes.description')</label>
                                <input wire:model="form.description" type="text" class="w-full rounded-lg border-gray-300"
                                    placeholder="@lang('product::messages.enter_description')" />
                                @error('form.description')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @if(!isset($type) || $type != "intermediate")
                                @if($showHasFabric)
                                    <div>
                                        <div class="text-sm">
                                            <label class="flex items-center" for="has_fabric">
                                                <input type="checkbox" wire:model="form.has_fabric" class="ml-2" />
                                                @lang('product::attributes.has_fabric_title')
                                            </label>
                                        </div>
                                        @error('form.has_fabric')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                @if($enableSelectFrameColor)
                                    <div>
                                        <div class="text-sm">
                                            <label class="flex items-center" for="has_frame">
                                                <input type="checkbox" wire:model="form.has_frame" class="ml-2" />
                                                @lang('product::attributes.has_frame_title')
                                            </label>
                                        </div>
                                        @error('form.has_frame')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($step === 2)
                {{-- Step 2: Product Specifications --}}
                <div>
                    <h2 class="mb-6 text-xl font-bold">@lang('product::attributes.add_product_materials')</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium">@lang('product::messages.select_raw_material')</label>
                            <div class="relative">
                                <select wire:model.lazy="rawMaterialForm.id"
                                    class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">@lang('core::messages.choose')</option>
                                    @foreach ($rawMaterials as $rawMaterial)
                                        <option value="{{$rawMaterial->id}}">{{$rawMaterial->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('rawMaterialForm.id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                @lang('product::attributes.required_quantity')
                                @if ($rawMaterialUnit)
                                    ({{$rawMaterialUnit?->name}})
                                @endif
                            </label>
                            <div class="relative">
                                <input type="number" step="any" min="0.1" wire:model="rawMaterialForm.qty"
                                    class="w-full rounded-lg border-gray-300"
                                    placeholder="@lang('product::messages.enter_raw_material_qty')" />
                            </div>
                            @error('rawMaterialForm.qty')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-Core::button type="button" wire:click="addRawMaterialRow" target="addRawMaterialRow"
                                class="bg-gray-700 font-semibold text-white hover:bg-gray-800">
                                @lang('product::attributes.add')
                            </x-Core::button>
                        </div>
                    </div>
                    <div class="relative overflow-x-auto w-full pb-2 mt-2">
                        <!-- Shadow to indicate scrollability on mobile -->
                        <div
                            class="absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-white to-transparent pointer-events-none z-10 md:hidden">
                        </div>
                        <!-- Outer wrapper handles the rounded border -->
                        @if(count($product->materials))
                            <div class="rounded-2xl overflow-hidden min-w-[650px] md:min-w-0">
                                <table class="w-full text-sm text-gray-700 border-collapse">
                                    <thead class="divide-x-4 divide-white">
                                        <tr class="text-center divide-x-4 divide-white">
                                            <th class="bg-amber-100/60 px-4 py-3 font-semibold w-16 whitespace-nowrap">
                                                @lang('core::attributes.row')
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-24 whitespace-nowrap">
                                                @lang('warehouse::attributes.code')
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold min-w-[120px] whitespace-nowrap">
                                                @lang('warehouse::attributes.product_name')
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-20 whitespace-nowrap">
                                                @lang('product::attributes.required_quantity')
                                            </th>
                                            {{-- <th class="bg-amber-100/60 px-3 py-3 font-semibold w-32 whitespace-nowrap">
                                                فی (ریال)
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-36 whitespace-nowrap">
                                                مبلغ نهایی (ریال)
                                            </th> --}}
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-24 whitespace-nowrap">
                                                @lang('product::attributes.actions')
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y-4 divide-white [&_td]:bg-[#F6F6F5] [&_td]:p-3">
                                        @foreach ($product->materials as $productMaterial)
                                            <tr class="text-center divide-x-4 divide-white">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $productMaterial->code }}</td>
                                                <td>{{ $productMaterial->title }}</td>
                                                <td>{{ $productMaterial->pivot->qty }}</td>
                                                {{-- <td class="text-center whitespace-nowrap">
                                                    {{ number_format($productMaterial->price) }}
                                                </td>
                                                <td class="text-center whitespace-nowrap">
                                                    {{number_format($productMaterial->price * $productMaterial->pivot->qty)}}
                                                </td> --}}
                                                <td class="px-2 whitespace-nowrap">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <!-- Delete Button -->
                                                        <button wire:click="removeProductMaterialRow({{ $productMaterial->id }})"
                                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                            title="حذف">
                                                            <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/trash.svg') }}"
                                                                alt="" class="w-5 h-5" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    {{-- <tfoot class="border-t-4 border-white">
                                        <tr class="divide-x-4 divide-white">
                                            <td colspan="4" class="px-4 py-3 text-left text-gray-500"></td>

                                            <!-- برچسب -->
                                            <td
                                                class="px-4 py-4 text-center font-semibold bg-[#B67E36] text-white rounded-br-xl">
                                                مبلغ نهایی
                                            </td>

                                            <!-- مبلغ: ادغام دو ستون آخر -->
                                            <td colspan="2" class="px-4 py-3 text-center font-bold bg-[#F6F6F5] rounded-bl-xl">
                                                <span>{{number_format($materialTotalPrice)}}</span>
                                            </td>
                                        </tr>
                                    </tfoot> --}}
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h2 class="my-6 text-xl font-bold">@lang('product::messages.select_material_from_intermediate_products')
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium">@lang('product::messages.select_intermediate_product')</label>
                            <div class="relative">
                                <select wire:model.lazy="intermediateProductForm.id"
                                    class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">@lang('core::messages.choose')</option>
                                    @foreach ($intermediateProducts as $intermediateProduct)
                                        <option value="{{$intermediateProduct->id}}">{{$intermediateProduct->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('intermediateProductForm.id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                @lang('product::attributes.count')
                            </label>
                            <div class="relative">
                                <input type="number" step="any" min="0.1" wire:model="intermediateProductForm.qty"
                                    class="w-full rounded-lg border-gray-300"
                                    placeholder="@lang('product::messages.enter_raw_material_qty')" />
                            </div>
                            @error('intermediateProductForm.qty')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-Core::button type="button" wire:click="addIntermediateProductRow"
                                target="addIntermediateProductRow"
                                class="bg-gray-700 font-semibold text-white hover:bg-gray-800">
                                @lang('product::attributes.add')
                            </x-Core::button>
                        </div>
                    </div>
                    <div class="relative overflow-x-auto w-full pb-2 mt-2">
                        <!-- Shadow to indicate scrollability on mobile -->
                        <div
                            class="absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-white to-transparent pointer-events-none z-10 md:hidden">
                        </div>
                        <!-- Outer wrapper handles the rounded border -->
                        @if(count($product->intermediate_products))
                            <div class="rounded-2xl overflow-hidden min-w-[650px] md:min-w-0">
                                <table class="w-full text-sm text-gray-700 border-collapse">
                                    <thead class="divide-x-4 divide-white">
                                        <tr class="text-center divide-x-4 divide-white">
                                            <th class="bg-amber-100/60 px-4 py-3 font-semibold w-16 whitespace-nowrap">
                                                @lang('core::attributes.row')
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-24 whitespace-nowrap">
                                                @lang('warehouse::attributes.code')
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold min-w-[120px] whitespace-nowrap">
                                                @lang('warehouse::attributes.product_name')
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-20 whitespace-nowrap">
                                                @lang('product::attributes.required_quantity')
                                            </th>
                                            {{-- <th class="bg-amber-100/60 px-3 py-3 font-semibold w-32 whitespace-nowrap">
                                                فی (ریال)
                                            </th>
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-36 whitespace-nowrap">
                                                مبلغ نهایی (ریال)
                                            </th> --}}
                                            <th class="bg-amber-100/60 px-3 py-3 font-semibold w-24 whitespace-nowrap">
                                                @lang('product::attributes.actions')
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y-4 divide-white [&_td]:bg-[#F6F6F5] [&_td]:p-3">
                                        @foreach ($product->intermediate_products as $intermediateProduct)
                                            <tr class="text-center divide-x-4 divide-white">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $intermediateProduct->code }}</td>
                                                <td>{{ $intermediateProduct->title }}</td>
                                                <td>{{ $intermediateProduct->pivot->qty }}</td>
                                                {{-- <td class="text-center whitespace-nowrap">
                                                    {{ number_format($productMaterial->price) }}
                                                </td>
                                                <td class="text-center whitespace-nowrap">
                                                    {{number_format($productMaterial->price * $productMaterial->pivot->qty)}}
                                                </td> --}}
                                                <td class="px-2 whitespace-nowrap">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <!-- Delete Button -->
                                                        <button
                                                            wire:click="removeIntermediateProductMaterialRow({{ $intermediateProduct->id }})"
                                                            class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                            title="حذف">
                                                            <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/trash.svg') }}"
                                                                alt="" class="w-5 h-5" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    {{-- <tfoot class="border-t-4 border-white">
                                        <tr class="divide-x-4 divide-white">
                                            <td colspan="4" class="px-4 py-3 text-left text-gray-500"></td>

                                            <!-- برچسب -->
                                            <td
                                                class="px-4 py-4 text-center font-semibold bg-[#B67E36] text-white rounded-br-xl">
                                                مبلغ نهایی
                                            </td>

                                            <!-- مبلغ: ادغام دو ستون آخر -->
                                            <td colspan="2" class="px-4 py-3 text-center font-bold bg-[#F6F6F5] rounded-bl-xl">
                                                <span>{{number_format($materialTotalPrice)}}</span>
                                            </td>
                                        </tr>
                                    </tfoot> --}}
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                @if($product->has_fabric)
                    <div>
                        <h2 class="my-6 text-xl font-bold">@lang('product::attributes.add_fabric')</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    @lang('product::messages.enter_title')
                                </label>
                                <div class="relative">
                                    <input type="text" wire:model="fabricForm.title" class="w-full rounded-lg border-gray-300"
                                        placeholder="@lang('product::messages.enter_fabric_title')" />
                                </div>
                                @error('fabricForm.title')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    @lang('product::attributes.required_fabric_quantity')
                                    ( @lang('product::attributes.meter') )
                                </label>
                                <div class="relative">
                                    <input type="number" step="any" min="0.1" wire:model="fabricForm.qty" class="w-full rounded-lg border-gray-300"
                                        placeholder="@lang('product::messages.enter_raw_material_qty')" />
                                </div>
                                @error('fabricForm.qty')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <x-Core::button type="button" wire:click="addFabricRow" target="addFabricRow"
                                    class="bg-gray-700 font-semibold text-white hover:bg-gray-800">
                                    @lang('product::attributes.add')
                                </x-Core::button>
                            </div>
                        </div>
                        <div class="relative overflow-x-auto w-full pb-2 mt-2">
                            <!-- Shadow to indicate scrollability on mobile -->
                            <div
                                class="absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-white to-transparent pointer-events-none z-10 md:hidden">
                            </div>
                            <!-- Outer wrapper handles the rounded border -->
                            @if(count($product->required_fabrics))
                                <div class="rounded-2xl overflow-hidden min-w-[650px] md:min-w-0">
                                    <table class="w-full text-sm text-gray-700 border-collapse">
                                        <thead class="divide-x-4 divide-white">
                                            <tr class="text-center divide-x-4 divide-white">
                                                <th class="bg-amber-100/60 px-4 py-3 font-semibold w-16 whitespace-nowrap">
                                                    @lang('core::attributes.row')
                                                </th>
                                                <th class="bg-amber-100/60 px-3 py-3 font-semibold w-24 whitespace-nowrap">
                                                    @lang('product::attributes.title')
                                                </th>
                                                <th class="bg-amber-100/60 px-3 py-3 font-semibold min-w-[120px] whitespace-nowrap">
                                                    @lang('product::attributes.qty')
                                                </th>
                                                <th class="bg-amber-100/60 px-3 py-3 font-semibold w-24 whitespace-nowrap">
                                                    @lang('product::attributes.actions')
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y-4 divide-white [&_td]:bg-[#F6F6F5] [&_td]:p-3">
                                            @foreach ($product->required_fabrics as $requiredFabric)
                                                <tr class="text-center divide-x-4 divide-white">
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>{{ $requiredFabric->title }}</td>
                                                    <td>{{ $requiredFabric->qty }}</td>
                                                    <td class="px-2 whitespace-nowrap">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <!-- Delete Button -->
                                                            <button wire:click="removeFabricRow({{ $requiredFabric->id }})"
                                                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                                title="حذف">
                                                                <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/trash.svg') }}"
                                                                    alt="" class="w-5 h-5" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
            @if($step === 3)
                {{-- {{! Step 3: Sales Conditions }} --}}
                <div>
                    <h2 class="mb-6 text-xl font-bold">@lang('product::attributes.add_production_process')</h2>
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium">@lang('product::messages.select_production_process')</label>
                        <div class="flex items-center gap-2">
                            <div class="relative  flex-1">
                                <select wire:model="hallId"
                                    class="w-full pr-10 pl-3 text-right rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">@lang('product::messages.choose')</option>
                                    @foreach ($halls as $hall)
                                        <option value="{{$hall->id}}">{{$hall->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input wire:model="sort" type="number" min="1" class="w-full rounded-lg border-gray-300"
                                    placeholder="@lang('product::messages.enter_production_sort')" />
                                @error('sort')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <x-Core::button type="button" wire:click="storeStation" target="storeStation"
                                class="flex-shrink-0 bg-gray-700 font-semibold text-white hover:bg-gray-800">
                                @lang('product::attributes.add')
                            </x-Core::button>
                        </div>
                        @error('hallId')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        @if ($product->hall_difinations->count())
                            <div>
                                <div
                                    class="relative mt-3 w-full max-h-[90vh] bg-white rounded-xl shadow-2xl flex flex-col overflow-hidden border border-gray-200">

                                    <div class="p-6 overflow-y-auto flex-1">
                                        <!-- Process steps management section -->
                                        <div class="mb-6">
                                            <div class="flex items-center justify-between mb-3">
                                                <h3 class="text-sm font-medium text-gray-700">
                                                    @lang('product::attributes.production_process_steps')
                                                </h3>
                                            </div>

                                            <!-- Steps list container -->
                                            <div class="flex flex-col gap-4">
                                                @forelse ($product->hall_difinations as $hallDefine)
                                                    <div
                                                        class="relative group bg-gray-50 rounded-lg p-3 border border-gray-200 hover:border-emerald-300 transition-colors">
                                                        <div class="flex items-center gap-3">
                                                            <div
                                                                class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center">
                                                                {{ $hallDefine->sort }}
                                                            </div>

                                                            <div class="flex-1">
                                                                {{$hallDefine->hall->title}}
                                                            </div>

                                                            <button type="button"
                                                                wire:click="removeStation({{ $hallDefine->hall_id }})"
                                                                class="p-1.5 text-gray-400 hover:text-red-500 rounded-full hover:bg-red-50 transition-colors"
                                                                @if(count($product->hall_difinations) <= 1) disabled
                                                                class="opacity-50 cursor-not-allowed" @endif
                                                                title="@lang('product::attributes.delete')">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-4 text-gray-500">
                                                        @lang('product::messages.without_production_process_step')
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @if($step === 4)
                {{-- {{! Step 3: Sales Conditions }} --}}
                <div>
                    <h2 class="mb-6 text-xl font-bold">@lang('product::attributes.add_product_sale_options')</h2>
                    @foreach ($costItems as $costItem)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3 items-end">
                            <div class="md:col-span-3">
                                <label class="mb-2 block text-sm font-medium">{{ $costItem->title }}</label>

                                <input wire:model.live="form.cost_items.{{ $costItem->id }}" type="number" min="0"
                                    class="w-full rounded-lg border-gray-300"
                                    placeholder="@lang('product::messages.enter_multiplier')" />

                                @error('form.cost_items.' . $costItem->id)
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="text-left block text-sm font-medium ml-3">
                                {{ number_format(((float) ($form['cost_items'][$costItem->id] ?? 0) * $costItem->base_price)) }}
                                {{ $currency }}
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif
            <div class="mt-6 flex items-center justify-between">
                <div class="flex items-center gap-2"></div>
                <div class="flex items-center gap-2">
                    {{-- مرحله قبل --}}
                    <x-Core::button type="button" wire:click="prev" :disabled="$step === 1" target="prev"
                        class="border border-gray-300 text-sm hover:bg-gray-50 disabled:opacity-40">
                        @lang('product::attributes.prev_step')
                    </x-Core::button>
                    {{-- مرحله بعد --}}
                    @if($step < $maxStep)
                        <x-Core::button type="button" wire:click="next" target="next"
                            class="bg-[#20BF86] font-semibold text-white hover:bg-[#1a9f72] disabled:opacity-40">
                            @lang('product::attributes.next_step')
                        </x-Core::button>
                    @elseif($step == $maxStep)
                        @can('products_publish')
                            <x-Core::button type="button" wire:click="publishProduct" target="publishProduct"
                                class="bg-[#20BF86] font-semibold text-white hover:bg-[#1a9f72] disabled:opacity-40">
                                @lang('product::attributes.confirm_and_publish_product')
                            </x-Core::button>
                        @endcan
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
    @vite('Modules/Core/resources/assets/js/utils.js')
@endpush
