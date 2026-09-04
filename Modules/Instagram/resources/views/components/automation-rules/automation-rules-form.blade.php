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

            {{-- @if ($currentStep === 'automation_actions')
            <div class="relative z-[1] space-y-3">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div class="space-y-3">
                        <x-dashboard::forms.select label="product::attributes.packaging_type"
                            name="automation_actionsForm.packaging_type" wire:model.defer="automation_actionsForm.packaging_type"
                            :options="$packagingType" option-value="id"
                            placeholder="product::messages.select_packaging_type" />

                        <x-dashboard::forms.input label="product::attributes.volume_ml" name="automation_actionsForm.volume_ml"
                            wire:model.defer="automation_actionsForm.volume_ml" placeholder="product::messages.enter_volume_ml"
                            :suffix="__('product::attributes.ml')" />

                    </div>
                    <div class="space-y-3">

                        <x-dashboard::forms.input label="product::attributes.base_sale_price" :suffix="$currency"
                            name="automation_actionsForm.price" wire:model.defer="automation_actionsForm.price"
                            placeholder="product::messages.enter_base_sale_price" />

                        <x-dashboard::forms.radio label="product::attributes.is_active" name="automation_actionsForm.is_active"
                            wire:model.defer="automation_actionsForm.is_active" :options="[
                    '1' => 'product::attributes.active',
                    '0' => 'product::attributes.inactive',
                ]" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <x-dashboard::buttons.primary-action id="btn-store-product-automation_actions" tag="button"
                        wire:click="storeProductautomation_actions" size="sm" class="btn-fill">
                        @lang('core::attributes.store')
                    </x-dashboard::buttons.primary-action>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4">
                <x-dashboard::table.table>
                    <x-slot:head>
                        <tr>
                            <th>@lang('core::attributes.row')</th>
                            <th>@lang('product::attributes.packaging_type')</th>
                            <th>@lang('product::attributes.volume_ml') (@lang('product::attributes.ml'))</th>
                            <th>@lang('product::attributes.base_sale_price') ({{$currency}})</th>
                            <th>@lang('product::attributes.is_active')</th>
                            <th>@lang('product::attributes.created_at')</th>
                            <th class="col-actions"></th>
                        </tr>
                    </x-slot:head>
                    <x-slot:body>
                        @foreach ($product->automation_actionss as $automation_actions)
                        <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                            <x-dashboard::table.cell :label="__('core::attributes.row')">
                                {{ $loop->index + 1 }}
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('product::attributes.packaging_type')">
                                {{$automation_actions->packaging_type->label()}}
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('product::attributes.volume_ml')">
                                {{number_format($automation_actions->volume_ml)}}
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('product::attributes.base_sale_price')">
                                {{number_format($automation_actions->price)}}
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('product::attributes.is_active')">
                                @if($automation_actions->is_active)
                                <span class="chip chip-ok">@lang('product::attributes.active')</span>
                                @else
                                <span class="chip chip-fail">@lang('product::attributes.inactive')</span>
                                @endif
                            </x-dashboard::table.cell>

                            <x-dashboard::table.cell :label="__('product::attributes.created_at')">
                                {{$automation_actions->created_at_jalali_date}}
                            </x-dashboard::table.cell>

                            <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                                <div class="flex gap-1">
                                    <x-dashboard::buttons.primary-action id="btn-delete-product-automation_actions-{{$automation_actions->automation_actions}}"
                                        tag="button" wire:click="deleteProductautomation_actions({{$automation_actions->id}})" size="sm">
                                        <img src="{{ asset('icons/dashboard/vuesax/outline/trash.svg') }}" alt="add"
                                            class="w-5" />
                                    </x-dashboard::buttons.primary-action>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </x-slot:body>
                </x-dashboard::table.table>
            </div>
            @endif --}}

            <x-slot:footer>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        @if ($currentStep !== 'basic')
                            <x-dashboard::buttons.primary-action id="btn-previous-step" tag="button"
                                class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
                                wire:click="previousStep" size="sm">
                                @lang('core::attributes.previous')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>

                    <div class="ms-auto">
                        @if ($currentStep !== 'automation_actions')
                            <x-dashboard::buttons.primary-action id="btn-next-step" tag="button"
                                class="rounded-xl btn-fill px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                wire:click="nextStep" size="sm">
                                @lang('core::attributes.next')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>
                </div>
            </x-slot:footer>
        </x-dashboard::forms.stepper>
    </form>
</section>
