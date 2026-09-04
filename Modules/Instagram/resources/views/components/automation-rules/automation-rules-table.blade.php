<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="$title">
        <x-slot:icon>
            <img src="{{ asset('icons/sidebar/magic-stick.svg') }}"
                alt="@lang('instagram::attributes.automation_rules_list')" />
        </x-slot:icon>

        {{-- فیلترها --}}
        {{-- <livewire:instagram::instagram-advanced-filters /> --}}

        <x-dashboard::buttons.primary-action id="btn-add-automation-rules" tag="a" class="btn-fill btn-new-tx shrink-0"
            href="{{ route('user.automation_rules.create') }}">
            <x-slot:icon>
                <img src="{{ asset('icons/header/add.svg') }}" alt="create_automation_rule" />
            </x-slot:icon>
            @lang('instagram::attributes.create_automation_rule')
        </x-dashboard::buttons.primary-action>
    </x-dashboard::card.card-header>

    <div>
        <x-dashboard::table.table>
            <x-slot:head>
                <tr>
                    <th>@lang('core::attributes.row')</th>
                    <th>@lang('instagram::attributes.title')</th>
                    <th>@lang('instagram::attributes.instagram_username')</th>
                    <th>@lang('instagram::attributes.post_title')</th>
                    <th>@lang('instagram::attributes.trigger_type')</th>
                    <th>@lang('instagram::attributes.match_type')</th>
                    <th>@lang('instagram::attributes.match_value')</th>
                    <th>@lang('instagram::attributes.is_active')</th>
                    <th>@lang('instagram::attributes.priority')</th>
                    <th>@lang('instagram::attributes.created_at')</th>
                    <th class="col-actions"></th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse($automationRules as $automationRule)
                    <tr class="data-row" data-status="success">
                        {{-- ردیف --}}
                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                            {{ toPersianNumber(($automationRules->currentPage() - 1) * $automationRules->perPage() + $loop->iteration) }}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.title')">
                            {{toPersianNumber($automationRule->name)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.instagram_username')">
                            {{$automationRule->instagramAccount->username}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.post_title')">
                            {{$automationRule->instagramPost ? 'پست خاص' : 'همه پست ها'}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.trigger_type')">
                            <x-dashboard::badge :color="$automationRule->trigger_type->color()">
                                {{$automationRule->trigger_type->label()}}
                            </x-dashboard::badge>
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.match_type')">
                            <x-dashboard::badge :color="$automationRule->match_type->color()">
                                {{$automationRule->match_type->label()}}
                            </x-dashboard::badge>
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.match_value')">
                            {{toPersianNumber($automationRule->match_value)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.is_active')">
                            <x-dashboard::badge :color="$automationRule->is_active ? 'green' : 'red'">
                                {{$automationRule->is_active ? 'فعال' : 'غیرفعال'}}
                            </x-dashboard::badge>
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.priority')">
                            {{toPersianNumber($automationRule->priority)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.created_at')">
                            {{toPersianNumber($automationRule->created_at_jalali)}}
                        </x-dashboard::table.cell>

                        <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                            <div class="flex gap-1">
                                @isset($editRouteName)
                                <x-dashboard::buttons.primary-action
                                    id="btn-automation-rule-{{ $automationRule->id }}-edit" tag="a"
                                    href="{{ route($editRouteName, ['automationRule' => $automationRule]) }}" size="sm">
                                    <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="edit-automation-rule"
                                        class="w-5" />
                                </x-dashboard::buttons.primary-action>
                                @endisset
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
        {{ $automationRules->links('Core::pagination') }}

    </div>

    {{$slot}}

</section>
