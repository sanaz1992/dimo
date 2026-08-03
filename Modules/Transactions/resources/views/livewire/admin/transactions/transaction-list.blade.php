<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('transactions::attributes.transactions')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\card-pos.svg') }}" alt="orders" />
        </x-slot:icon>
    </x-dashboard::card.card-header>


    <x-dashboard::table.table>
        <x-slot:head>
            <tr>
                <th>@lang('core::attributes.row')</th>
                <th>@lang('transactions::attributes.order_code')</th>
                <th>@lang('transactions::attributes.gateway')</th>
                <th>@lang('transactions::attributes.status')</th>
                <th>@lang('transactions::attributes.reference_id')</th>
                <th>@lang('transactions::attributes.amount')({{ $currency }})</th>
                <th>@lang('transactions::attributes.created_at')</th>
                <th class="col-actions"></th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach ($transactions as $transaction)
                <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                    <x-dashboard::table.cell :label="__('core::attributes.row')">
                        {{ toPersianNumber($loop->index + 1 )}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('transactions::attributes.order_code')">
                        {{toPersianNumber($transaction->order->order_number)}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('transactions::attributes.gateway')">
                        {{$transaction->gateway}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('transactions::attributes.status')">
                        <x-dashboard::badge :color="$transaction->status->color()">
                            {{$transaction->status->label()}}</x-dashboard::badge>
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('transactions::attributes.reference_id')">
                            {{$transaction->reference_id}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('transactions::attributes.amount')">
                        {{formatPrice($transaction->amount)}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('transactions::attributes.created_at')">
                        {{toPersianNumber($transaction->created_at_jalali)}}
                    </x-dashboard::table.cell>

                    <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                        <div class="flex gap-1">
                            <x-dashboard::buttons.primary-action id="btn-show-transaction-{{$transaction->id}}" tag="a"
                                href="{{ route('admin.transactions.show', $transaction) }}" size="sm">
                                <img src="{{ asset('icons/dashboard/vuesax/outline/eye.svg') }}" alt="show" class="w-5" />
                            </x-dashboard::buttons.primary-action>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-dashboard::table.table>
</section>
