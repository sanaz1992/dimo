<section class="panel p-5 anim-fade-up">
    <h3 class="relative z-[1] mb-4 text-base font-bold text-ink">{{$title}}</h3>
    <form wire:submit.prevent="store">
        <div class="relative z-[1] space-y-3">
            <x-dashboard::forms.input label="user::attributes.name" name="form.name" wire:model.defer="form.name" />

            <x-dashboard::forms.input label="user::attributes.mobile" name="form.mobile"
                wire:model.defer="form.mobile" />

            <x-dashboard::forms.input label="user::attributes.password" name="form.password" type="password"
                wire:model.defer="form.password" />

            <x-dashboard::forms.input label="user::attributes.password_confirmation" name="form.password_confirmation"
                type="password" wire:model.defer="form.password_confirmation" />

            <x-dashboard::forms.select label="user::attributes.level" name="form.level" wire:model.defer="form.level"
                :options="$userLevels" placeholder="user::messages.select_level" />

            <x-dashboard::buttons.primary-action id="btn-store-user" tag="button" type="submit" class="btn-fill">
                @lang('core::attributes.store')
            </x-dashboard::buttons.primary-action>
        </div>
    </form>
</section>
