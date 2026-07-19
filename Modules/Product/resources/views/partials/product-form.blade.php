<section class="panel p-5 anim-fade-up">
    <div
        class="table-toolbar relative z-[1] mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
        <h2 class="text-base font-bold text-ink sm:text-lg">{{ $title }}</h2>
    </div>

    <form wire:submit.prevent="store" class="space-y-4">
        <x-dashboard::forms.stepper :steps="$steps" :current-step="$currentStep">

            @if ($currentStep === 'basic')
                    <div class="relative z-[1] space-y-3">

                        <x-dashboard::forms.image-upload label="product::attributes.image" name="form.image"
                            wire:model="form.image" :preview="$this->imagePreview" :file-name="$this->clientOriginalName"
                            :upload-key="$imageUploadKey" :hint="__('media::attributes.image_formats') . ' ' . $imageConfig['mimes'] . ' / ' . __('media::attributes.max') . ' ' . $imageConfig['max'] / 1024 . ' ' . __('media::attributes.MB')" />

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-3">
                                <x-dashboard::forms.input label="product::attributes.name" name="form.name"
                                    wire:model.defer="form.name" />

                                <x-dashboard::forms.select label="product::attributes.grade" name="form.grade"
                                    wire:model.defer="form.grade" :options="$gardes"
                                    placeholder="product::messages.select_grade" />
                            </div>
                            <div class="space-y-3">
                                <x-dashboard::forms.select label="product::attributes.category" name="form.category_id"
                                    wire:model.defer="form.category_id" :options="$categories" option-value="id"
                                    placeholder="product::messages.select_category" />

                                <x-dashboard::forms.radio label="product::attributes.extraction_method"
                                    name="form.extraction_method" wire:model.defer="form.extraction_method"
                                    :options="$extractionMethods" />
                            </div>
                        </div>

                        <x-dashboard::forms.textarea label="product::attributes.description" name="form.description"
                            wire:model.defer="form.description" />

                        <x-dashboard::forms.radio label="product::attributes.is_active" name="form.is_active"
                            wire:model.defer="form.is_active" :options="[
                    '1' => 'product::attributes.active',
                    '0' => 'product::attributes.inactive',
                ]" />

                    </div>
            @endif

            @if ($currentStep === 'sku')
                    <div class="relative z-[1] space-y-3">

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                            <div class="space-y-3">
                                <x-dashboard::forms.select label="product::attributes.packaging_type"
                                    name="skuForm.packaging_type" wire:model.defer="skuForm.packaging_type"
                                    :options="$packagingType" option-value="id"
                                    placeholder="product::messages.select_packaging_type" />

                                <x-dashboard::forms.input label="product::attributes.volume_ml" name="skuForm.volume_ml"
                                    wire:model.defer="skuForm.volume_ml" placeholder="product::messages.enter_volume_ml"
                                    :suffix="__('product::attributes.ml')" />

                            </div>
                            <div class="space-y-3">

                                <x-dashboard::forms.input label="product::attributes.base_sale_price" :suffix="$currency"
                                    name="skuForm.price" wire:model.defer="skuForm.price"
                                    placeholder="product::messages.enter_base_sale_price" />

                                <x-dashboard::forms.radio label="product::attributes.is_active" name="skuForm.is_active"
                                    wire:model.defer="skuForm.is_active" :options="[
                    '1' => 'product::attributes.active',
                    '0' => 'product::attributes.inactive',
                ]" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <x-dashboard::buttons.primary-action id="btn-store-product-sku" tag="button"
                                wire:click="storeProductSku" size="sm" class="btn-fill">
                                @lang('core::attributes.store')
                            </x-dashboard::buttons.primary-action>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-4">
                        <x-dashboard::table.table>
                            <x-slot:head>
                                <tr>
                                    <th>@lang('core::attributes.row')</th>
                                    <th>@lang('product::attributes.packaging_type')</th>
                                    <th>@lang('product::attributes.volume_ml') (@lang('product::attributes.ml'))</th>
                                    <th>@lang('product::attributes.base_sale_price') ({{$currency}})</th>
                                    <th>@lang('product::attributes.is_active')</th>
                                    <th>@lang('product::attributes.created_at')</th>
                                    <th class="col-actions"></th>
                                </tr>
                            </x-slot:head>
                            <x-slot:body>
                                @foreach ($product->skus as $sku)
                                    <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                                            {{ $loop->index + 1 }}
                                        </x-dashboard::table.cell>

                                        <x-dashboard::table.cell :label="__('product::attributes.packaging_type')">
                                            {{$sku->packaging_type->label()}}
                                        </x-dashboard::table.cell>

                                        <x-dashboard::table.cell :label="__('product::attributes.volume_ml')">
                                            {{number_format($sku->volume_ml)}}
                                        </x-dashboard::table.cell>

                                        <x-dashboard::table.cell :label="__('product::attributes.base_sale_price')">
                                            {{number_format($sku->price)}}
                                        </x-dashboard::table.cell>

                                        <x-dashboard::table.cell :label="__('product::attributes.is_active')">
                                            @if($sku->is_active)
                                                <span class="chip chip-ok">@lang('product::attributes.active')</span>
                                            @else
                                                <span class="chip chip-fail">@lang('product::attributes.inactive')</span>
                                            @endif
                                        </x-dashboard::table.cell>

                                        <x-dashboard::table.cell :label="__('product::attributes.created_at')">
                                            {{$sku->created_at_jalali_date}}
                                        </x-dashboard::table.cell>

                                        <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                                            <div class="flex gap-1">
                                                <x-dashboard::buttons.primary-action id="btn-delete-product-sku-{{$sku->sku}}"
                                                    tag="button" wire:click="deleteProductSku({{$sku->id}})" size="sm">
                                                    <img src="{{ asset('icons/dashboard/vuesax/outline/trash.svg') }}" alt="add"
                                                        class="w-5" />
                                                </x-dashboard::buttons.primary-action>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot:body>
                        </x-dashboard::table.table>
                    </div>
            @endif

            <x-slot:footer>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        @if ($currentStep !== 'basic')
                            <x-dashboard::buttons.primary-action id="btn-previous-step" tag="button"
                                class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
                                wire:click="previousStep" size="sm">
                                @lang('core::attributes.previous')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>

                    <div class="ms-auto">
                        @if ($currentStep !== 'sku')
                            <x-dashboard::buttons.primary-action id="btn-next-step" tag="button"
                                class="rounded-xl btn-fill px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                wire:click="nextStep" size="sm">
                                @lang('core::attributes.next')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>
                </div>
            </x-slot:footer>
        </x-dashboard::forms.stepper>
    </form>

</section>
