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
    </style>
@endpush

<div class="container mx-auto px-4 rtl">
    <!-- Main product wizard container with Alpine.js component -->
    <div class="w-full">
        <!-- Progress indicator navigation showing 3 steps -->
        <x-Product::product-nav :step="$step" :max-step="$maxStep" />

        <section class="rounded-xl border border-gray-200 bg-white p-4 sm:p-6 shadow-sm">
            <form wire:submit.prevent="store">
                {{-- step 1 --}}
                @if($step === 1)
                    <div>
                        <h2 class="mb-4 text-xl font-bold">
                            @lang('product::attributes.product_create')
                            @if($type == "intermediate")
                                @lang('product::attributes.intermediate')
                            @endif
                        </h2>

                        <div class="flex flex-col gap-4">
                            {{-- <livewire:media.image-upload-input :form="$form" name="image" /> --}}
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-900 mb-1">@lang('user::attributes.image')</label>
                                    <p class="text-xs text-gray-500 mb-2">
                                        @lang('media::attributes.image_formats'):
                                        {{config('media.validations.image.mimes')}}
                                        (@lang('media::attributes.max') {{config('media.validations.image.max') / 1024}}
                                        @lang('media::attributes.MB'))
                                    </p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <div class="w-full flex-1">
                                        <label
                                            class="flex flex-col items-center justify-center w-full h-40 border rounded-3xl cursor-pointer hover:bg-gray-100 transition-colors border-[#D3E0E4] relative overflow-hidden
                                                                                                {{isset($form['image']) ? 'border-green-500 bg-green-50' : ''}}">
                                            @if (isset($form['image']) && is_object($form['image']))
                                                <div class="w-full h-full flex items-center justify-center p-2 text-center">
                                                    <div>
                                                        <svg class="mx-auto h-10 w-10 text-green-500 mb-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
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
                                                <div
                                                    class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                                                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                        </path>
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500">
                                                        <span class="font-semibold">
                                                            @lang('media::messages.click_for_upload') </span>
                                                        @lang('media::messages.or_drop_image')
                                                    </p>
                                                </div>
                                            @endif
                                            <input type="file"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                accept="image/jpeg, image/png, image/jpg" wire:model="form.image" />
                                        </label>
                                    </div>
                                    @if (isset($form['image']) && is_object($form['image']))
                                        <div class="w-[200px] flex-shrink-0">
                                            <div
                                                class="h-40 w-full rounded-3xl overflow-hidden border border-[#D3E0E4] relative group">
                                                <img src="{{ $form['image']->temporaryUrl() }}"
                                                    alt=" @lang('media::attributes.image_preview')"
                                                    class="h-full w-full object-cover" />
                                                <div
                                                    class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 text-white text-xs p-1.5 text-center truncate">
                                                    {{ $form['image']->getClientOriginalName() }}
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
                                        <label
                                            class="mb-2 block text-sm font-medium">@lang('product::attributes.title')</label>
                                        <input wire:model="form.title" type="text" class="w-full rounded-lg border-gray-300"
                                            placeholder="@lang('product::messages.enter_title')" />
                                        @error('form.title')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium">@lang('product::attributes.code')</label>
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
                                                    placeholder="{{ __('warehouse::messages.enter_price') }}"
                                                    oninput="formatNumberInput(this, 'form.price')" />

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
                                            <select wire:model="form.order_type"
                                                wire:change="changeOrderType($event.target.value)"
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
                                    <input wire:model="form.description" type="text"
                                        class="w-full rounded-lg border-gray-300"
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
                                {{-- <div>
                                    <label
                                        class="mb-2 block text-sm font-medium">{{__('product::attributes.status')}}</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                                        <label class="flex items-center cursor-pointer" for="is_active_on">
                                            <input type="radio" name="is_active" wire:model="form.is_active"
                                                id="is_active_on" value="1"
                                                class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500" />
                                            <span class="mr-2">{{__('product::attributes.active')}}</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer" for="is_active_off">
                                            <input type="radio" name="is_active" wire:model="form.is_active"
                                                id="is_active_off" value="0"
                                                class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500" />
                                            <span class="mr-2">{{__('product::attributes.deactive')}}</span>
                                        </label>
                                    </div>
                                    @error('form.is_active')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div> --}}
                                {{-- <div class="mb-4">
                                    <label class="block mb-1 text-sm font-medium">{{__('product::attributes.gallery')}}
                                        :</label>
                                    <input type="file" wire:model="form.gallery" multiple class="mb-4">
                                    @error('form.gallery.*')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div> --}}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center gap-2"></div>
                    <div class="flex items-center gap-2">
                        {{-- مرحله قبل --}}
                        <x-Core::button wire:click="prev" :disabled="$step === 1"
                            class="border border-gray-300 text-sm hover:bg-gray-50">
                            @lang('product::attributes.prev_step')
                        </x-Core::button>

                        {{-- مرحله بعد --}}
                        <x-Core::button type="submit" class="bg-[#20BF86] font-semibold text-white hover:bg-[#1a9f72]">
                            @lang('product::attributes.next_step')
                        </x-Core::button>

                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

@push('scripts')
    @vite('Modules/Core/resources/assets/js/utils.js')
@endpush
