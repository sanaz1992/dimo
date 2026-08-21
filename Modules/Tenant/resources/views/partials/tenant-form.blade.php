<section class="panel p-5 anim-fade-up">
    <h3 class="relative z-[1] mb-4 text-base font-bold text-ink">{{$title}}</h3>
    <form wire:submit.prevent="store">
        <div class="relative z-[1] space-y-3">
            <x-dashboard::forms.input label="tenant::attributes.name" name="form.name" wire:model.defer="form.name" />


            <x-dashboard::forms.select label="tenant::attributes.timezone" name="form.timezone" wire:model.defer="form.timezone"
                :options="$timezones" placeholder="tenant::messages.select_timezone" />

            <x-dashboard::forms.select label="tenant::attributes.local" name="form.local" wire:model.defer="form.local"
                :options="$locals" placeholder="tenant::messages.select_local" />

            <x-dashboard::buttons.primary-action id="btn-store-tenant" tag="button" type="submit" class="btn-fill">
                @lang('core::attributes.store')
            </x-dashboard::buttons.primary-action>
        </div>
    </form>
</section>
