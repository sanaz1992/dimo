<section class="panel p-5 anim-fade-up">
    <div
        class="table-toolbar relative z-[1] mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
        <h2 class="text-base font-bold text-ink sm:text-lg">{{ $title }}</h2>
    </div>

    <form wire:submit.prevent="store" class="space-y-4">
        <div class="relative z-[1] space-y-3">
            <x-dashboard::forms.image-upload label="product::attributes.image" name="form.image" wire:model="form.image"
                :preview="$this->imagePreview" :file-name="$this->clientOriginalName" :upload-key="$imageUploadKey"
                :hint="__('media::attributes.image_formats') . ' ' . $imageConfig['mimes'] . ' / ' . __('media::attributes.max') . ' ' . $imageConfig['max'] / 1024 . ' ' . __('media::attributes.MB')" />

            <x-dashboard::forms.input label="product::attributes.name" name="form.name" wire:model.defer="form.name" />

            <x-dashboard::forms.textarea label="product::attributes.description" name="form.description"
                wire:model.defer="form.description" />

            <x-dashboard::forms.radio label="product::attributes.is_active" name="form.is_active"
                wire:model.defer="form.is_active" :options="[
        '1' => 'product::attributes.active',
        '0' => 'product::attributes.inactive',
    ]" />

        </div>


        <div class="flex items-center justify-between gap-3">
            <div>
                <x-dashboard::buttons.primary-action id="btn-submit-form" tag="button"
                    class="btn-fill rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
                    type="submit" size="sm">
                    @lang('core::attributes.store')
                </x-dashboard::buttons.primary-action>
            </div>
        </div>

    </form>

</section>
