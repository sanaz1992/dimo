<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="$title">
        <x-slot:icon>
            <img src="{{ asset('icons/sidebar/clock.svg') }}"
                alt="@lang('instagram::attributes.automation_runs_list')" />
        </x-slot:icon>

        {{-- فیلترها --}}
        {{-- <livewire:instagram::instagram-advanced-filters /> --}}
    </x-dashboard::card.card-header>

    <div>
        <x-dashboard::table.table>
            <x-slot:head>
                <tr>
                    <th>@lang('core::attributes.row')</th>
                    <th>@lang('instagram::attributes.rule_title')</th>
                    <th>@lang('instagram::attributes.instagram_username')</th>
                    <th>@lang('instagram::attributes.customer_username')</th>
                    <th>@lang('instagram::attributes.comment_text')</th>
                    <th>@lang('instagram::attributes.status')</th>
                    <th>@lang('instagram::attributes.created_at')</th>
                    <th class="col-actions"></th>
                </tr>
            </x-slot:head>

            <x-slot:body>
                @forelse($automationRuns as $automationRun)
                    <tr class="data-row" data-status="success">
                        {{-- ردیف --}}
                        <x-dashboard::table.cell :label="__('core::attributes.row')">
                            {{ toPersianNumber(($automationRuns->currentPage() - 1) * $automationRuns->perPage() + $loop->iteration) }}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.rule_title')">
                            {{toPersianNumber($automationRun->automationRule->name)}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.instagram_username')">
                            {{$automationRun->instagramAccount->username}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.customer_username')">
                            {{$automationRun->instagramComment->commenter_username}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.comment_text')">
                            {{$automationRun->instagramComment->comment_text}}
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.status')">
                            <x-dashboard::badge :color="$automationRun->status->color()">
                                {{$automationRun->status->label() }}
                            </x-dashboard::badge>
                        </x-dashboard::table.cell>

                        <x-dashboard::table.cell :label="__('instagram::attributes.created_at')">
                            {{toPersianNumber($automationRun->created_at_jalali)}}
                        </x-dashboard::table.cell>

                        <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                            <div class="flex gap-1">
                                @if ($userCanShowDetail)
                                    <x-dashboard::buttons.primary-action id="btn-automation-run-{{ $automationRun->id }}-show"
                                        tag="button" wire:click="showRunDetail({{ $automationRun->id }})"
                                        target="showRunDetail({{ $automationRun->id }})" size="sm">
                                        <img src="{{ asset('icons/dashboard/vuesax/outline/eye.svg') }}"
                                            alt="edit-automation-rule" class="w-5" />
                                    </x-dashboard::buttons.primary-action>
                                @endif
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
        {{ $automationRuns->links('Core::pagination') }}

    </div>

    {{$slot}}


    @if($showRunActionsModal)
        <div class="modal-backdrop modal-backdrop--show" wire:click="$set('showRunActionsModal', false)">
            <div class="modal modal--show w-full max-w-4xl" role="dialog" aria-modal="true" wire:click.stop>
                <div class="modal-head">
                    <h2 class="text-lg font-bold text-ink">
                        @lang('instagram::attributes.detail_automation_run')
                        {{ $selectedRun->automationRule->name }} -
                        {{toPersianNumber($selectedRun->created_at_jalali) }}
                    </h2>
                    <button type="button" class="btn-ghost" aria-label="بستن"
                        wire:click="$set('showRunActionsModal', false)">
                        ×
                    </button>
                </div>

                <div class="modal-body space-y-5">
                    {{-- اطلاعات کلی اجرا --}}
                    <div class="rounded-xl border border-line bg-surface p-4">
                        <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
                            {{-- خودکارسازی --}}
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-ink-faint whitespace-nowrap">
                                    @lang('instagram::attributes.rule_title'):
                                </span>
                                <span class="text-sm font-semibold text-ink">
                                    {{ $selectedRun->automationRule->name }}
                                </span>
                            </div>

                            {{-- وضعیت --}}
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-ink-faint whitespace-nowrap">
                                    @lang('instagram::attributes.status'):
                                </span>
                                <x-dashboard::badge :color="$selectedRun->status->color()">
                                    {{ $selectedRun->status->label() }}
                                </x-dashboard::badge>
                            </div>

                            {{-- مشتری --}}
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-ink-faint whitespace-nowrap">
                                    @lang('instagram::attributes.customer_username'):
                                </span>
                                <span class="text-sm font-semibold text-ink">
                                    {{ $selectedRun->instagramComment?->commenter_username ?? '-' }}
                                </span>
                            </div>

                            {{-- تاریخ --}}
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-ink-faint whitespace-nowrap">
                                    @lang('instagram::attributes.created_at'):
                                </span>
                                <span class="text-sm font-semibold text-ink">
                                    {{ toPersianNumber($selectedRun->created_at_jalali) }}
                                </span>
                            </div>

                        </div>

                        {{-- اطلاعات مخصوص Admin --}}
                        @if($authUser->level == Modules\User\Enums\UserLevel::ADMIN ?? false)
                            <div class="mt-4 pt-4 border-t border-line">
                                <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
                                    {{-- Tenant --}}
                                    @if($selectedRun->tenant)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-ink-faint whitespace-nowrap">
                                                @lang('instagram::attributes.tenant_name'):
                                            </span>
                                            <span class="text-sm font-semibold text-ink">
                                                {{ $selectedRun->tenant->name }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- اکانت اینستاگرام --}}
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-ink-faint whitespace-nowrap">
                                            @lang('instagram::attributes.instagram_username'):
                                        </span>
                                        <span class="text-sm font-semibold text-ink">
                                            {{ $selectedRun->instagramAccount?->username ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- کامنت --}}
                    <div class="rounded-xl border border-line bg-surface p-4">
                        <div class="text-xs text-ink-faint mb-2">
                            @lang('instagram::attributes.comment_text')
                        </div>
                        <div class="text-sm leading-6 text-ink whitespace-pre-wrap break-words">
                            {{ $selectedRun->instagramComment?->comment_text ?? '-' }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-ink">
                                @lang('instagram::attributes.automation_action_runs')
                            </h3>
                            <span class="text-xs text-ink-faint">
                                {{ toPersianNumber($selectedRun->actionRuns->count()) }}
                                @lang('instagram::attributes.action')
                            </span>
                        </div>

                        <div class="space-y-3">
                            @forelse($selectedRun->actionRuns as $actionRun)
                                <div class="rounded-xl border border-line bg-surface p-4">
                                    {{-- عنوان Action + وضعیت --}}
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">

                                            {{-- action name --}}
                                            <div class="text-sm font-semibold text-ink">
                                                {{ $actionRun->automationAction?->name ?? '-' }}
                                            </div>

                                            {{-- Action type --}}
                                            @if($actionRun->automationAction?->action_type)
                                                <div class="text-xs text-ink-faint mt-1">
                                                    {{ $actionRun->automationAction->action_type->label() }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Action status --}}
                                        @if($actionRun->status)
                                            <x-dashboard::badge :color="$actionRun->status->color()">
                                                {{ $actionRun->status->label() }}
                                            </x-dashboard::badge>
                                        @endif
                                    </div>

                                    {{-- message text --}}
                                    @if(
                                            $actionRun->automationAction?->action_type === \Modules\Instagram\Enums\AutomationActionType::SEND_MESSAGE ||
                                            $actionRun->automationAction?->action_type === \Modules\Instagram\Enums\AutomationActionType::SEND_PRIVATE_REPLY
                                        )
                                        @if(!empty($actionRun->automationAction?->config['message']))
                                            <div class="mt-4 w-full rounded-lg border border-line bg-surface-muted px-4 py-3">
                                                <div class="text-xs font-semibold text-ink-faint mb-1.5">
                                                    @lang('instagram::attributes.message_text')
                                                </div>
                                                <div class="text-sm leading-6 text-ink whitespace-pre-wrap break-words">
                                                    {{ $actionRun->automationAction->config['message'] }}
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- run time --}}
                                    @if($actionRun->started_at || $actionRun->completed_at)
                                        <div class="flex flex-wrap gap-x-6 gap-y-2 mt-4 text-xs text-ink-faint">
                                            @if($actionRun->started_at)
                                                <div>
                                                    @lang('instagram::attributes.start'):
                                                    {{ toPersianNumber(verta($actionRun->started_at)->format('Y/m/d H:i:s')) }}
                                                </div>
                                            @endif
                                            @if($actionRun->completed_at)
                                                <div>
                                                    @lang('instagram::attributes.end'):
                                                    {{ toPersianNumber(verta($actionRun->completed_at)->format('Y/m/d H:i:s')) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- error --}}
                                    @if($actionRun->error)
                                        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                                            <div class="text-xs font-semibold text-red-600 mb-1">
                                                @lang('instagram::attributes.error')
                                            </div>
                                            <div class="text-xs leading-6 text-red-700">
                                                @lang('instagram::messages.this_operation_could_not_be_completed')
                                            </div>
                                        </div>
                                    @endif

                                    {{-- show Context just for Admin --}}
                                    @if(
                                            ($authUser->level == Modules\User\Enums\UserLevel::ADMIN ?? false)
                                            && $actionRun->context
                                        )
                                        <details class="mt-4">
                                            <summary class="cursor-pointer text-xs text-ink-faint">
                                                @lang('instagram::attributes.technical_execution_details')
                                            </summary>
                                            <div class="mt-3 rounded-lg bg-surface-muted p-3">
                                                <pre class="text-xs leading-6 whitespace-pre-wrap break-words">
                                                                                                    {{ json_encode($actionRun->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                                                                </pre>
                                            </div>
                                        </details>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-xl border border-line bg-surface p-8 text-center">
                                    <div class="text-sm text-ink-faint">
                                        @lang('instagram::messages.no_actions_have_been_recorded_for_this_execution_yet')
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
