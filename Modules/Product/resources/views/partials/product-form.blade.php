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

            <x-slot:footer>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        @if ($currentStep !== 'basic')
                            <button type="button" wire:click="previousStep"
                                class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                                قبلی
                            </button>
                        @endif
                    </div>

                    <div class="ms-auto">
                        @if ($currentStep !== 'sku')
                            <button type="button" wire:click="nextStep"
                                class="rounded-xl btn-fill px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90">
                                بعدی
                            </button>
                        @else
                            <x-dashboard::buttons.primary-action id="btn-store-product" tag="button" type="submit">
                                @lang('core::attributes.store')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>
                </div>
            </x-slot:footer>
        </x-dashboard::forms.stepper>
    </form>

</section>
