<section class="panel p-5 anim-fade-up">
    <h3 class="relative z-[1] mb-4 text-base font-bold text-ink">{{$title}}</h3>

    <form wire:submit.prevent="store" class="space-y-4">
        <x-dashboard::forms.stepper :steps="$steps" :current-step="$currentStep">

            @if ($currentStep === 'basic')
                    <div class="relative z-[1] space-y-3">

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-3">
                                <x-dashboard::forms.input label="instagram::attributes.automation_rule_name"
                                    wire:model.defer="form.name" />

                                <x-dashboard::forms.select label="instagram::attributes.instagram_account"
                                    wire:model.live="form.instagram_account" :options="$instagramAccounts"
                                    :option-value="'unique_code'" :option-label="'username'" placeholder="instagram::messages.select_instagram_account" />

                            </div>
                            <div class="space-y-3">
                                <x-dashboard::forms.select label="instagram::attributes.tenant" wire:model.live="form.tenant"
                                    :options="$tenants" :option-value="'slug'"
                                    placeholder="instagram::messages.select_tenant" />

                                <x-dashboard::forms.select label="instagram::attributes.instagram_post"
                                    wire:model.defer="form.instagram_post_id" :options="$instagramPosts"
                                    placeholder="instagram::messages.select_instagram_post" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-3">
                                <x-dashboard::forms.select label="instagram::attributes.match_type" wire:model.defer="form.match_type"
                                    :options="$matchTypes" placeholder="instagram::messages.select_match_type" />

                                <x-dashboard::forms.select label="instagram::attributes.trigger_type"
                                    wire:model.defer="form.trigger_type" :options="$triggerTypes"
                                    placeholder="instagram::messages.select_trigger_type" />

                            </div>
                            <div class="space-y-3">

                                <x-dashboard::forms.input label="instagram::attributes.match_value"
                                    wire:model.defer="form.match_value" />

                                <x-dashboard::forms.radio label="instagram::attributes.is_active" name="form.is_active"
                                    wire:model.defer="form.is_active" :options="[
                    '1' => 'core::attributes.active',
                    '0' => 'core::attributes.inactive',
                ]" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-3">
                                <x-dashboard::forms.input label="instagram::attributes.priority"
                                    wire:model.defer="form.priority"   type="number"  />
                            </div>
                        </div>


                    </div>
            @endif

            @if ($currentStep === 'automation_actions')
                <div class="relative z-[1] space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-3">
                            <x-dashboard::forms.select
                                label="instagram::attributes.action_type"
                                wire:model.live="actionForm.action_type"
                                :options="$actionTypes"
                                placeholder="instagram::messages.select_action_type"
                            />

                            @if ($actionForm['action_type'] === \Modules\Instagram\Enums\AutomationActionType::SEND_MESSAGE->value)
                                <x-dashboard::forms.textarea
                                    label="instagram::attributes.message"
                                    wire:model.defer="actionForm.message"
                                />
                            @endif
                        </div>
                        <div class="space-y-3">
                            <x-dashboard::forms.input
                                label="instagram::attributes.sort_order"
                                type="number"
                                wire:model.defer="actionForm.sort_order"
                            />

                            <x-dashboard::forms.radio
                                label="instagram::attributes.is_active"
                                name="actionForm.is_active"
                                wire:model.defer="actionForm.is_active"
                                :options="[
                                    '1' => 'core::attributes.active',
                                    '0' => 'core::attributes.inactive',
                                ]"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-dashboard::buttons.primary-action id="btn-add-automation-action" tag="button"
                            wire:click="addAutomationAction" target="addAutomationAction" size="sm" class="btn-fill">
                            @lang('instagram::attributes.create_action')
                        </x-dashboard::buttons.primary-action>
                    </div>
                </div>
                <div class="mt-6 border-t border-slate-100 pt-4">
                    <x-dashboard::table.table>
                        <x-slot:head>
                            <tr>
                                <th>@lang('core::attributes.row')</th>
                                <th>@lang('instagram::attributes.action_type')</th>
                                <th>@lang('instagram::attributes.message')</th>
                                <th>@lang('instagram::attributes.sort_order')</th>
                                <th>@lang('instagram::attributes.is_active')</th>
                                <th class="col-actions"></th>
                            </tr>
                        </x-slot:head>
                        <x-slot:body>
                            @foreach ($automationRule->actions as $action)
                                <tr class="data-row">
                                    <x-dashboard::table.cell :label="__('core::attributes.row')">
                                        {{ $loop->iteration }}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('instagram::attributes.action_type')">
                                        {{ $action->action_type->label() }}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('instagram::attributes.message')">
                                        @if ($action->action_type === \Modules\Instagram\Enums\AutomationActionType::SEND_MESSAGE)
                                            {{ $action->config['message'] ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('instagram::attributes.sort_order')">
                                        {{ $action->sort_order }}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('instagram::attributes.is_active')">
                                        @if ($action->is_active)
                                            <span class="chip chip-ok">
                                                @lang('core::attributes.active')
                                            </span>
                                        @else
                                            <span class="chip chip-fail">
                                                @lang('core::attributes.inactive')
                                            </span>
                                        @endif
                                    </x-dashboard::table.cell>

                                    <td class="data-cell px-4 py-3.5 col-actions" data-label="@lang('core::attributes.actions')">
                                        <div class="flex gap-1">
                                            <x-dashboard::buttons.primary-action
                                                id="btn-edit-automation-action-{{ $action->id }}" tag="button"
                                                wire:click="editAutomationAction({{ $action->id }})" size="sm"
                                                target="editAutomationAction({{ $action->id }})"
                                                 :title="__('core::attributes.edit')">
                                                <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="edit" class="w-5" />
                                            </x-dashboard::buttons.primary-action>

                                            <x-dashboard::buttons.primary-action
                                                id="btn-delete-automation-action-{{ $action->id }}" tag="button"
                                                wire:click="deleteAutomationAction({{ $action->id }})" size="sm"
                                                target="deleteAutomationAction({{ $action->id }})"
                                                :title="__('core::attributes.delete')" >
                                                <img src="{{ asset('icons/dashboard/vuesax/outline/trash.svg') }}" alt="delete" class="w-5" />
                                            </x-dashboard::buttons.primary-action>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot:body>
                    </x-dashboard::table.table>
                </div>
            @endif

            <x-slot:footer>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        @if ($currentStep !== 'basic')
                            <x-dashboard::buttons.primary-action id="btn-previous-step" tag="button"
                                class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
                                wire:click="previousStep" target="previousStep" size="sm">
                                @lang('core::attributes.previous')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>

                    <div class="ms-auto">
                        @if ($currentStep !== 'automation_actions')
                            <x-dashboard::buttons.primary-action id="btn-next-step" tag="button"
                                class="rounded-xl btn-fill px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                wire:click="nextStep" target="nextStep" size="sm">
                                @lang('core::attributes.next')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>
                </div>
            </x-slot:footer>
        </x-dashboard::forms.stepper>
    </form>

      @if($showActionModal)
        <div class="modal-backdrop modal-backdrop--show" wire:click="$set('showActionModal', false)">
            <div class="modal modal--show" role="dialog" aria-modal="true" wire:click.stop>
                <div class="modal-head">
                    <h2 class="text-lg font-bold text-ink">
                        @lang('core::attributes.edit')
                        {{ $selectedEditingAction?->action_type->label() }}
                    </h2>
                    <button type="button" class="btn-ghost" aria-label="بستن"
                        wire:click="$set('showActionModal', false)">
                        ×
                    </button>
                </div>

                <form class="modal-body space-y-3">
                    <x-dashboard::forms.select
                        label="instagram::attributes.action_type"
                        wire:model.live="editActionForm.action_type"
                        :options="$actionTypes"
                        placeholder="instagram::messages.select_action_type"
                    />

                    <x-dashboard::forms.input
                        label="instagram::attributes.sort_order"
                        type="number"
                        wire:model.defer="editActionForm.sort_order"
                    />

                    <x-dashboard::forms.radio
                        label="instagram::attributes.is_active"
                        wire:model.defer="editActionForm.is_active"
                        :options="[
                            '1' => 'core::attributes.active',
                            '0' => 'core::attributes.inactive',
                        ]"
                    />

                    @if ($editActionForm['action_type'] === \Modules\Instagram\Enums\AutomationActionType::SEND_MESSAGE)
                        <x-dashboard::forms.textarea label="instagram::attributes.message" wire:model.defer="editActionForm.message"/>
                    @endif

                    <x-dashboard::buttons.primary-action id="btn-update-item-status" tag="button"
                        wire:click="updateAutomationAction" size="sm" class="btn-fill" target="updateAutomationAction">
                        @lang('instagram::attributes.update_action')
                    </x-dashboard::buttons.primary-action>

                </form>
            </div>
        </div>
    @endif
</section>
