<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('inventory::attributes.purchases')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\products.svg') }}" alt="purchases" />
        </x-slot:icon>
        <x-dashboard::buttons.primary-action id="btn-add-purchase" tag="a" class="btn-fill btn-new-tx shrink-0"
            href="{{ route('admin.purchases.create') }}">
            <x-slot:icon>
                <img src="{{ asset('icons/header/add.svg') }}" alt="purchases" />
            </x-slot:icon>
            @lang('inventory::attributes.create_purchase')
        </x-dashboard::buttons.primary-action>

    </x-dashboard::card.card-header>


    <x-dashboard::table.table>
        <x-slot:head>
            <tr>
                <th>@lang('core::attributes.row')</th>
                <th>@lang('inventory::attributes.supplier')</th>
                <th>@lang('inventory::attributes.purchase_items_count')</th>
                <th>@lang('inventory::attributes.purchased_at')</th>
                <th>@lang('inventory::attributes.created_at')</th>
                <th class="col-actions"></th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach ($purchases as $purchase)
                <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                    <x-dashboard::table.cell :label="__('core::attributes.row')">
                        {{ toPersianNumber($loop->index + 1) }}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('inventory::attributes.supplier')">
                        {{$purchase->supplier->name}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('inventory::attributes.purchase_items_count')" >
                        {{formatPrice($purchase->items->count())}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('inventory::attributes.purchased_at')">
                        {{toPersianNumber($purchase->purchased_at_jalali)}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('inventory::attributes.created_at')">
                        {{toPersianNumber($purchase->created_at_jalali)}}
                    </x-dashboard::table.cell>

                    <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                        <div class="flex gap-1">
                            <x-dashboard::buttons.primary-action id="btn-edit-purchase-{{$purchase->id}}" tag="a"
                                href="{{ route('admin.purchases.edit', $purchase) }}" size="sm">
                                <img src="{{ asset('icons/dashboard/vuesax/outline/edit-2.svg') }}" alt="add" class="w-5" />
                            </x-dashboard::buttons.primary-action>

                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-dashboard::table.table>
</section>
