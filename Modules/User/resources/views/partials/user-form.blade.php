<section class="panel p-5 anim-fade-up">
    <h3 class="relative z-[1] mb-4 text-base font-bold text-ink">{{$title}}</h3>
    <form wire:submit.prevent="store">
        <div class="relative z-[1] space-y-3">
            <label class="block">
                <span class="mb-1 block text-[12px] text-ink-faint">@lang('user::attributes.name')</span>
                <input class="input-field" wire:model.defer="form.name">
                @error('form.name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </label>
            <label class="block">
                <span class="mb-1 block text-[12px] text-ink-faint">@lang('user::attributes.mobile')</span>
                <input class="input-field" wire:model.defer="form.mobile">
                @error('form.mobile')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </label>
            <label class="block">
                <span class="mb-1 block text-[12px] text-ink-faint">@lang('user::attributes.password')</span>
                <input class="input-field" type="password" wire:model.defer="form.password">
                @error('form.password')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </label>
            <label class="block">
                <span
                    class="mb-1 block text-[12px] text-ink-faint">@lang('user::attributes.password_confirmation')</span>
                <input class="input-field" type="password" wire:model.defer="form.password_confirmation">
                @error('form.password_confirmation')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </label>

            <x-dashboard::buttons.primary-action id="btn-store-user" tag="button" type="submit">
                @lang('core::attributes.store')
            </x-dashboard::buttons.primary-action>
        </div>
    </form>
</section>
