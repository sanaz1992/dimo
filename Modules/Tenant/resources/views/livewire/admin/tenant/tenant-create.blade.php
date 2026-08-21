<x-Tenant::tenant.tenant-form :title="__('tenant::attributes.create_tenant')" :timezones="$timezones" :locals="$locals">
    <x-dashboard::forms.select label="tenant::attributes.user" wire:model.defer="form.user" :options="$users"
        placeholder="tenant::messages.select_user" :option-value="'unique_code'" />
</x-Tenant::tenant.tenant-form>