<section class="panel p-5 anim-fade-up">
    <h3 class="relative z-[1] mb-4 text-base font-bold text-ink">@lang('core::attributes.settings')</h3>
    <form wire:submit.prevent="update">
        <div class="relative z-[1] space-y-3">
            @foreach ($settings as $setting)
                @switch($setting->type)
                    @case(Modules\Core\Enums\SettingType::IMAGE->value)
                    @php
                        $label='core::attributes.setting_items.'.$setting->key ;
                    @endphp
                        <x-dashboard::forms.image-upload :label="$label" name="form.image"
                            wire:model="form.image"
                            :hint="__('media::attributes.image_formats') . ' ' . $imageConfig['mimes'] . ' / ' . __('media::attributes.max') . ' ' . $imageConfig['max'] / 1024 . ' ' . __('media::attributes.MB')" />

                    @break
                    @case(Modules\Core\Enums\SettingType::BOOL->value)
                    @php
                        $name='form.'. $setting->key ;
                    @endphp
                        <x-dashboard::forms.checkbox
                            :text="$setting->title"
                            :name="$name"
                            wire:model.defer="form.{{ $setting->key }}"
                            orientation="horizontal"
                        />
                    @break
                    @default
                    @php
                        $name='form.'. $setting->key ;
                    @endphp
                        <x-dashboard::forms.input :label="$setting->title"  :name="$name"
                            wire:model.defer="form.{{ $setting->key }}" />
                @endswitch
            @endforeach
        </div>

        <div class="mt-6 border-t border-slate-100 pt-4">
            <x-dashboard::buttons.primary-action id="btn-update-settings" tag="button" type="submit"
                class="btn-fill btn-new-tx shrink-0">
                @lang('core::attributes.update')
            </x-dashboard::buttons.primary-action>
        </div>
    </form>
</section>