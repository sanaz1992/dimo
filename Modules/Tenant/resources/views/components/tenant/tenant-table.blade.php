<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="$title">
        <x-slot:icon>
            <img src="{{ asset('icons/sidebar/manager.svg') }}" alt="tenants" />
        </x-slot:icon>

        {{-- فیلترها --}}
        <livewire:tenant::tenant-advanced-filters />

        @isset($createRouteName)
            <x-dashboard::buttons.primary-action id="btn-add-user" tag="a" class="btn-fill btn-new-tx shrink-0"
                href="{{ route($createRouteName) }}">
                <x-slot:icon>
                    <img src="{{ asset('icons/header/add.svg') }}" alt="tenants" />
                </x-slot:icon>

                @lang('tenant::attributes.create_tenant')
            </x-dashboard::buttons.primary-action>
        @endisset

    </x-dashboard::card.card-header>

    <div>
        <x-dashboard::table.table>
            <x-slot:head>
                <tr>
                    <th>@lang('core::attributes.row')</th>
                    <th>@lang('tenant::attributes.name')</th>
                    <th>@lang('tenant::attributes.timezone')</th>
                    <th>@lang('tenant::attributes.local')</th>
                    <th>@lang('tenant::attributes.status')</th>
                    <th>@lang('tenant::attributes.created_at')</th>
                    <th class="col-actions"></th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse($tenants as $tenant)
                    <tr class="data-row" data-status="success">
                        {{-- ردیف --}}
                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                            {{ toPersianNumber(($tenants->currentPage() - 1) * $tenants->perPage() + $loop->iteration) }}
                        </x-dashboard::table.cell>

                        {{-- نام --}}
                        <x-dashboard::table.cell :label="__('tenant::attributes.name')">
                            {{ $tenant->name }}
                        </x-dashboard::table.cell>

                        {{-- timezone --}}
                        <x-dashboard::table.cell :label="__('tenant::attributes.timezone')">
                            {{ $tenant->timezone }}
                        </x-dashboard::table.cell>

                        {{-- local --}}
                        <x-dashboard::table.cell :label="__('tenant::attributes.local')">
                            {{ $tenant->local }}
                        </x-dashboard::table.cell>

                        {{-- وضعیت --}}
                        <x-dashboard::table.cell :label="__('tenant::attributes.status')">
                            @if(isset($canEditStatus) && $canEditStatus)
                                <x-dashboard::badge :color="$tenant->status->color()" class="cursor-pointer"
                                    wire:click="selectStatus({{ $tenant->id }})">
                                    {{ $tenant->status->label() }}
                                </x-dashboard::badge>
                            @else
                                <x-dashboard::badge :color="$tenant->status->color()">
                                    {{ $tenant->status->label() }}
                                </x-dashboard::badge>
                            @endif
                        </x-dashboard::table.cell>

                        {{-- تاریخ --}}
                        <x-dashboard::table.cell :label="__('tenant::attributes.created_at')">
                            {{ toPersianNumber($tenant->created_at_jalali_date) }}
                        </x-dashboard::table.cell>

                        {{-- عملیات --}}
                        <td class="data-cell px-4 py-3.5 col-actions" data-label="{{ __('core::attributes.actions') }}">

                            <div class="flex gap-1">
                                @isset($editRouteName)
                                    <x-dashboard::buttons.primary-action id="btn-edit-tenant-{{ $tenant->id }}" tag="a"
                                        href="{{ route($editRouteName, $tenant) }}" size="sm">
                                        <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="edit"
                                            class="w-5" />
                                    </x-dashboard::buttons.primary-action>
                                @endisset
                                @isset($instagramRouteName)
                                    {{-- Instagram --}}
                                    <x-dashboard::buttons.primary-action id="btn-tenant-{{ $tenant->id }}-instagram-accounts"
                                        tag="a" href="{{ route($instagramRouteName, ['tenant' => $tenant]) }}" size="sm">
                                        <img src="{{ asset('icons/dashboard/instagram.svg') }}" alt="instagram" class="w-5" />
                                    </x-dashboard::buttons.primary-action>
                                @endisset
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-[13px] text-ink-faint">
                            @lang('core::messages.no_data')
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-dashboard::table.table>

        {{-- Pagination --}}
        {{ $tenants->links('Core::pagination') }}

    </div>

    {{$slot}}

</section>