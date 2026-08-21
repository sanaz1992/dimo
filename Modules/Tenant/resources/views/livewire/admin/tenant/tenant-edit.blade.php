
<x-Tenant::tenant.tenant-form :title="__('tenant::attributes.edit_tenant').' ' . $tenant->name" :timezones="$timezones" :locals="$locals">
    <x-dashboard::forms.select label="tenant::attributes.user" wire:model.defer="form.user" :options="$users"
        placeholder="tenant::messages.select_user" :option-value="'unique_code'" />
</x-Tenant::tenant.tenant-form>
