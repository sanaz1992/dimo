<x-Tenant::tenant.tenant-table :title="__('tenant::attributes.tenants_list')" :tenants="$tenants" :create-route-name="'admin.tenants.create'"
    :edit-route-name="'admin.tenants.edit'" :instagram-route-name="'admin.instagram_accounts.index'" :can-edit-status="true">

    @if($showChangeStatusModal)
        <div class="modal-backdrop modal-backdrop--show" wire:click="$set('showChangeStatusModal', false)">
            <div class="modal modal--show" role="dialog" aria-modal="true" wire:click.stop>
                <div class="modal-head">
                    <h2 class="text-lg font-bold text-ink">
                        @lang('core::attributes.edit')
                        {{ $selectedTenant?->name }}
                    </h2>
                    <button type="button" class="btn-ghost" aria-label="بستن"
                        wire:click="$set('showChangeStatusModal', false)">
                        ×
                    </button>
                </div>

                <form class="modal-body space-y-3">
                    <x-dashboard::forms.select label="tenant::attributes.status" name="form.status"
                        wire:model.defer="form.status" :options="$tenantStatuses"
                        placeholder="tenant::messages.select_status" />

                    <x-dashboard::buttons.primary-action id="btn-update-item-status" tag="button"
                        wire:click="updateItemStatus" size="sm" class="btn-fill" wire:target="updateItemStatus">
                        @lang('tenant::attributes.update_status')
                    </x-dashboard::buttons.primary-action>

                </form>
            </div>
        </div>
    @endif
</x-Tenant::tenant.tenant-table>