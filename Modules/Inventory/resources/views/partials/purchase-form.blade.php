<section class="panel p-5 anim-fade-up">
    <div
        class="table-toolbar relative z-[1] mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
        <h2 class="text-base font-bold text-ink sm:text-lg">{{ $title }}</h2>
        @isset($purchase)
            <span>@lang('inventory::attributes.invoice_number') : {{ toPersianNumber($purchase->invoice_number )}}</span>
        @endisset
    </div>

    <form wire:submit.prevent="store" class="space-y-4">
        <x-dashboard::forms.stepper :steps="$steps" :current-step="$currentStep">

            @if ($currentStep === 'basic')
                <div class="relative z-[1] space-y-3">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-3">
                            <x-dashboard::forms.input label="inventory::attributes.invoice_number"
                                name="itemsForm.invoice_number" wire:model.defer="form.invoice_number"
                                placeholder="inventory::messages.enter_invoice_number"
                                :suffix="__('inventory::messages.enter_the_invoice_number_if_available_otherwise_it_will_be_generated_automatically')" />

                            <x-dashboard::forms.select label="inventory::attributes.supplier" name="form.supplier_id"
                                wire:model.defer="form.supplier_id" :options="$suppliers"
                                placeholder="inventory::messages.select_supplier" />

                        </div>
                        <div class="space-y-3">

                            <x-dashboard::forms.input label="inventory::attributes.purchased_at" name="form.purchased_at"
                                class="persianDate" wire:model.defer="form.purchased_at"
                                placeholder="inventory::messages.select_purchased_at" />

                            <x-dashboard::forms.select label="inventory::attributes.status" name="form.status"
                                wire:model.defer="form.status" :options="$purchaseStatuses"
                                placeholder="inventory::messages.select_status" />
                        </div>
                    </div>



                </div>
            @endif

            @if ($currentStep === 'products')
                <div class="relative z-[1] space-y-3">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-3">
                            <x-dashboard::forms.select label="inventory::attributes.product" name="itemsForm.product_id"
                                wire:model.live="itemsForm.product_id" :options="$products" option-value="id"
                                placeholder="inventory::messages.select_product" />

                            <x-dashboard::forms.input label="product::attributes.volume_ml" name="itemsForm.volume_ml"
                                wire:model.defer="itemsForm.volume_ml" placeholder="product::messages.enter_volume_ml"
                                :suffix="__('product::attributes.ml')" />
                        </div>
                        <div class="space-y-3">
                            <x-dashboard::forms.select label="inventory::attributes.packaging_type"
                                name="itemsForm.packaging_type" wire:model.defer="itemsForm.packaging_type"
                                placeholder="inventory::messages.select_packaging_type" :options="$packagingTypes" />

                            <x-dashboard::forms.input label="product::attributes.quantity" name="itemsForm.quantity"
                                wire:model.defer="itemsForm.quantity" placeholder="product::messages.enter_quantity" />
                        </div>
                        <div class="space-y-3">
                            <x-dashboard::forms.input label="inventory::attributes.purchase_price" :suffix="$currency"
                                name="itemsForm.purchase_price" wire:model.defer="itemsForm.purchase_price"
                                placeholder="inventory::messages.enter_purchase_price" />
                        </div>
                        <div class="space-y-3">
                            <x-dashboard::forms.input label="inventory::attributes.sale_price" :suffix="$currency"
                                name="itemsForm.sale_price" wire:model.defer="itemsForm.sale_price"
                                placeholder="inventory::messages.enter_sale_price" />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <x-dashboard::buttons.primary-action id="btn-store-product-sku" tag="button"
                            wire:click="storeProduct" size="sm" class="btn-fill" wire:target="storeProduct">
                            @lang('inventory::attributes.store_product')
                        </x-dashboard::buttons.primary-action>
                    </div>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-4">
                    <x-dashboard::table.table>
                        <x-slot:head>
                            <tr>
                                <th>@lang('core::attributes.row')</th>
                                <th>@lang('inventory::attributes.product')</th>
                                <th>@lang('inventory::attributes.packaging_type')</th>
                                <th>@lang('product::attributes.volume_ml') (@lang('product::attributes.ml'))</th>
                                <th>@lang('product::attributes.quantity')</th>
                                <th>@lang('inventory::attributes.purchase_price')({{$currency}})</th>
                                <th>@lang('inventory::attributes.sale_price') ({{$currency}})</th>
                                <th>@lang('inventory::attributes.created_at')</th>
                                <th class="col-actions"></th>
                            </tr>
                        </x-slot:head>
                        <x-slot:body>
                            @foreach ($purchase->items as $item)
                                <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                                    <x-dashboard::table.cell :label="__('core::attributes.row')">
                                        {{ toPersianNumber($loop->index + 1) }}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('inventory::attributes.product')">
                                        {{$item->product_sku->product->name}}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('inventory::attributes.packaging_type')">
                                        {{$item->product_sku->packaging_type->label()}}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('inventory::attributes.volume_ml')">
                                        {{formatPrice($item->product_sku->volume_ml)}}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('inventory::attributes.quantity')">
                                        {{formatPrice($item->quantity)}}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('inventory::attributes.purchase_price')">
                                        {{formatPrice($item->purchase_price)}}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('inventory::attributes.sale_price')">
                                        {{formatPrice($item->sale_price)}}
                                    </x-dashboard::table.cell>

                                    <x-dashboard::table.cell :label="__('inventory::attributes.created_at')">
                                        {{toPersianNumber($item->created_at_jalali_date)}}
                                    </x-dashboard::table.cell>

                                    <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                                        <div class="flex gap-1">
                                            @if($purchase->status == Modules\Inventory\Enums\PurchaseStatus::DRAFT->value)
                                                <x-dashboard::buttons.primary-action id="btn-delete-item-{{$item->id}}" tag="button"
                                                    wire:click="deleteItem({{$item->id}})" size="sm"
                                                    wire:target="deleteItem({{$item->id}})">
                                                    <img src="{{ asset('icons/dashboard/vuesax/outline/trash.svg') }}" alt="add"
                                                        class="w-5" />
                                                </x-dashboard::buttons.primary-action>

                                                <x-dashboard::buttons.primary-action id="btn-edit-item-{{$item->id}}" tag="button"
                                                    wire:click="editItem({{$item->id}})" size="sm" wire:target="editItem">
                                                    <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="add"
                                                        class="w-5" />
                                                </x-dashboard::buttons.primary-action>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot:body>
                        <x-slot:footer >
                            <tr class="bg-slate-100">
                                <x-dashboard::table.cell class="justify-start" colspan="4"></x-dashboard::table.cell>

                                <x-dashboard::table.cell class="justify-start"
                                    :label="__('inventory::attributes.total_cost')">
                                    @lang('inventory::attributes.total_cost')
                                </x-dashboard::table.cell>

                                <x-dashboard::table.cell :label="__('inventory::attributes.total_cost')">
                                    {{ formatPrice($purchase->items->sum('total_cost')) }}
                                </x-dashboard::table.cell>

                                <x-dashboard::table.cell class="justify-start" colspan="3"></x-dashboard::table.cell>
                            </tr>
                        </x-slot:footer>
                    </x-dashboard::table.table>
                </div>
            @endif

            <x-slot:footer>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        @if ($currentStep !== 'basic')
                            <x-dashboard::buttons.primary-action id="btn-previous-step" tag="button"
                                class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 btn-fill-padding"
                                wire:click="previousStep" size="sm" wire:target="previousStep">
                                @lang('core::attributes.previous')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>

                    <div class="ms-auto">
                        @if ($currentStep !== 'sku')
                            <x-dashboard::buttons.primary-action id="btn-next-step" tag="button"
                                class="rounded-xl btn-fill px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                wire:click="nextStep" size="sm" wire:target="nextStep">
                                @lang('core::attributes.next')
                            </x-dashboard::buttons.primary-action>
                        @endif
                    </div>
                </div>
            </x-slot:footer>
        </x-dashboard::forms.stepper>
    </form>

    @if($showEditItemModal)
        <div class="modal-backdrop modal-backdrop--show" wire:click="$set('showEditItemModal', false)">
            <div class="modal modal--show" role="dialog" aria-modal="true" wire:click.stop>

                <div class="modal-head">
                    <h2 id="modal-tx-title" class="text-lg font-bold text-ink">
                        @lang('core::attributes.edit')
                        {{ $selectedPurchaseItem?->product_sku->product->name }}
                        -
                        {{ $selectedPurchaseItem?->product_sku->packaging_type->label()}}
                        -
                        {{number_format($selectedPurchaseItem?->product_sku->volume_ml)}} (@lang('product::attributes.ml'))
                    </h2>
                    <button type="button" data-modal-close class="btn-ghost" aria-label="بستن"
                        wire:click="$set('showEditItemModal', false)">
                        <span data-icon="close" data-icon-size="sm"><svg xmlns="http://www.w3.org/2000/svg" width="18"
                                height="18" viewBox="0 0 24 24" fill="none" class="icon-svg shrink-0" aria-hidden="true">
                                <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round"></path>
                            </svg></span>
                    </button>
                </div>

                <form id="form-tx" class="modal-body space-y-3">
                    <x-dashboard::forms.input label="product::attributes.quantity" name="editItemForm.quantity"
                        wire:model.defer="editItemForm.quantity" placeholder="product::messages.enter_quantity" />

                    <x-dashboard::forms.input label="inventory::attributes.purchase_price" :suffix="$currency"
                        name="editItemForm.purchase_price" wire:model.defer="editItemForm.purchase_price"
                        placeholder="inventory::messages.enter_purchase_price" />

                    <x-dashboard::forms.input label="inventory::attributes.sale_price" :suffix="$currency"
                        name="editItemForm.sale_price" wire:model.defer="editItemForm.sale_price"
                        placeholder="inventory::messages.enter_sale_price" />

                    <x-dashboard::buttons.primary-action id="btn-update-item" tag="button" wire:click="updateItem" size="sm"
                        class="btn-fill" wire:target="updateItem">
                        @lang('inventory::attributes.update_product')
                    </x-dashboard::buttons.primary-action>
                </form>

            </div>
        </div>
    @endif
</section>
