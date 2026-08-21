<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('instagram::attributes.instagram_accounts_list')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\instagram-white.svg') }}"
                alt="@lang('instagram::attributes.instagram_accounts_list')" />
        </x-slot:icon>

        {{-- <livewire:instagram::instagram-advanced-filters /> --}}

    </x-dashboard::card.card-header>

    <div>
        <x-dashboard::table.table>
            <x-slot:head>
                <tr>
                    <th>@lang('core::attributes.row')</th>
                    <th>@lang('instagram::attributes.tenant_name')</th>
                    <th>@lang('instagram::attributes.name')</th>
                    <th>@lang('instagram::attributes.token_expires_at')</th>
                    <th>@lang('instagram::attributes.status')</th>
                    <th>@lang('instagram::attributes.connected_at')</th>
                    <th>@lang('instagram::attributes.last_synced_at')</th>
                    <th class="col-actions"></th>
                </tr>
            </x-slot:head>
            <x-slot:body>
                @foreach ($instagramAccounts as $instagramAccount)
                    <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                            {{toPersianNumber($loop->index + 1)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.tenant_name')">
                            {{$instagramAccount->tenant->name}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.name')">
                            {{$instagramAccount->name}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.token_expires_at')">
                            {{$instagramAccount->token_expires_at_jalali}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.status')">
                            <x-dashboard::badge :color="$instagramAccount->status->color()">
                                {{$instagramAccount->status->label()}}
                            </x-dashboard::badge>
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.connected_at')">
                            {{toPersianNumber($instagramAccount->connected_at_jalali)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.last_synced_at')">
                            {{toPersianNumber($instagramAccount->last_synced_at_jalali)}}
                        </x-dashboard::table.cell>

                        <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                            <div class="flex gap-1">

                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-dashboard::table.table>
        {{$instagramAccounts->links('Core::pagination')}}
    </div>
</section>
