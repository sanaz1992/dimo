<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('user::attributes.tenants_list')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\manager.svg') }}" alt="tenants" />
        </x-slot:icon>
        <x-dashboard::buttons.primary-action id="btn-add-user" tag="a" class="btn-fill btn-new-tx shrink-0"
            href="{{ route('admin.tenants.create') }}">
            <x-slot:icon>
                <img src="{{ asset('icons/header/add.svg') }}" alt="tenants" />
            </x-slot:icon>
            @lang('user::attributes.create_tenant')
        </x-dashboard::buttons.primary-action>
    </x-dashboard::card.card-header>


    <x-dashboard::table.table>
        <x-slot:head>
            <tr>
                <th>@lang('core::attributes.row')</th>
                <th>@lang('user::attributes.name')</th>
                <th>@lang('user::attributes.timezone')</th>
                <th>@lang('user::attributes.local')</th>
                <th>@lang('user::attributes.status')</th>
                <th>@lang('user::attributes.created_at')</th>
                <th class="col-actions"></th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach ($tenants as $tenant)
                <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                    <x-dashboard::table.cell :label="__('core::attributes.row')">
                        {{toPersianNumber($loop->index + 1)}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('user::attributes.name')">
                        {{$tenant->name}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('user::attributes.timezone')">
                        {{$tenant->timezone}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('user::attributes.local')">
                        {{$tenant->local}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('user::attributes.status')">
                        <x-dashboard::badge :color="$tenant->status->color()" class="cursor-pointer" wire:click="selectStatus({{$tenant->id}})">
                            {{$tenant->status->label()}}
                        </x-dashboard::badge>
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('user::attributes.created_at')">
                        {{toPersianNumber($tenant->created_at_jalali_date)}}
                    </x-dashboard::table.cell>

                    <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                        <div class="flex gap-1">
                            <x-dashboard::buttons.primary-action id="btn-edit-tenant-{{$tenant->id}}" tag="a"
                                href="{{ route('admin.tenants.edit', $tenant) }}" size="sm">
                                <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="add" class="w-5" />
                            </x-dashboard::buttons.primary-action>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-dashboard::table.table>

    @if($showChangeStatusModal)
        <div class="modal-backdrop modal-backdrop--show" wire:click="$set('showChangeStatusModal', false)">
            <div class="modal modal--show" role="dialog" aria-modal="true" wire:click.stop>

                <div class="modal-head">
                    <h2 id="modal-tx-title" class="text-lg font-bold text-ink">
                        @lang('core::attributes.edit')
                        {{ $selectedTenant?->name }}
                      </h2>
                    <button type="button" data-modal-close class="btn-ghost" aria-label="بستن"
                        wire:click="$set('showChangeStatusModal', false)">
                        <span data-icon="close" data-icon-size="sm"><svg xmlns="http://www.w3.org/2000/svg" width="18"
                                height="18" viewBox="0 0 24 24" fill="none" class="icon-svg shrink-0" aria-hidden="true">
                                <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round"></path>
                            </svg>
                        </span>
                    </button>
                </div>

                <form id="form-tx" class="modal-body space-y-3">
                    <x-dashboard::forms.select label="user::attributes.status" name="form.status" wire:model.defer="form.status"
                        :options="$tenantStatuses" placeholder="user::messages.select_status" />

                    <x-dashboard::buttons.primary-action id="btn-update-item-status" tag="button" wire:click="updateItemStatus" size="sm"
                        class="btn-fill" wire:target="updateItemStatus">
                        @lang('user::attributes.update_status')
                    </x-dashboard::buttons.primary-action>
                </form>

            </div>
        </div>
    @endif
</section>
