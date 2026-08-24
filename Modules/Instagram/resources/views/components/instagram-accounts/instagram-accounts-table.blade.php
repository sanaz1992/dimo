<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="$title">
        <x-slot:icon>
            <img src="{{ asset('icons/sidebar/instagram-white.svg') }}"
                alt="@lang('instagram::attributes.instagram_accounts_list')" />
        </x-slot:icon>

        {{-- فیلترها --}}
          {{-- <livewire:instagram::instagram-advanced-filters /> --}}

    </x-dashboard::card.card-header>

    <div>
        <x-dashboard::table.table>
            <x-slot:head>
                <tr>
                    <th>@lang('core::attributes.row')</th>
                    <th>@lang('instagram::attributes.tenant_name')</th>
                    <th>@lang('instagram::attributes.username')</th>
                    <th>@lang('instagram::attributes.name')</th>
                    <th>@lang('instagram::attributes.token_expires_at')</th>
                    <th>@lang('instagram::attributes.status')</th>
                    <th>@lang('instagram::attributes.connected_at')</th>
                    <th>@lang('instagram::attributes.last_synced_at')</th>
                    <th class="col-actions"></th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse($instagramAccounts as $instagramAccount)
                    <tr class="data-row" data-status="success">
                        {{-- ردیف --}}
                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                            {{ toPersianNumber(($instagramAccounts->currentPage() - 1) * $instagramAccounts->perPage() + $loop->iteration) }}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.tenant_name')">
                            {{$instagramAccount->tenant->name}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.username')">
                            {{($instagramAccount->username)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.name')">
                            {{toPersianNumber($instagramAccount->name)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.token_expires_at')">
                            {{toPersianNumber($instagramAccount->token_expires_at_jalali)}}
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
                @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-[13px] text-ink-faint">
                            @lang('core::messages.no_data')
                        </td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-dashboard::table.table>

        {{-- Pagination --}}
        {{ $instagramAccounts->links('Core::pagination') }}

    </div>

    {{$slot}}

</section>
