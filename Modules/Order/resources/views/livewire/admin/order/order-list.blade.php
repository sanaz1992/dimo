<section class="table-panel anim-fade-up">

    <x-dashboard::card.card-header :title="__('order::attributes.orders')">
        <x-slot:icon>
            <img src="{{ asset('icons\sidebar\orders.svg') }}" alt="orders" />
        </x-slot:icon>
    </x-dashboard::card.card-header>


    <x-dashboard::table.table>
        <x-slot:head>
            <tr>
                <th>@lang('core::attributes.row')</th>
                <th>@lang('order::attributes.order_code')</th>
                <th>@lang('order::attributes.customer_name')</th>
                <th>@lang('order::attributes.status')</th>
                <th>@lang('order::attributes.payment_status')</th>
                <th>@lang('order::attributes.total_amount')({{ $currency }})</th>
                <th>@lang('order::attributes.created_at')</th>
                <th class="col-actions"></th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @foreach ($orders as $order)
                <tr class="data-row" data-searchable="" data-status="success" style="animation-delay:0.35s">
                    <x-dashboard::table.cell :label="__('core::attributes.row')">
                        {{ toPersianNumber($loop->index + 1 )}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('order::attributes.order_code')">
                        {{toPersianNumber($order->order_number)}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('order::attributes.customer_name')">
                        {{$order->user->name}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('order::attributes.status')">
                        <x-dashboard::badge :color="$order->status->color()">
                            {{$order->status->label()}}</x-dashboard::badge>
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('order::attributes.payment_status')">
                        <x-dashboard::badge :color="$order->payment_status->color()">
                            {{$order->payment_status->label()}}</x-dashboard::badge>
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('order::attributes.total_amount')">
                        {{formatPrice($order->total_amount)}}
                    </x-dashboard::table.cell>

                    <x-dashboard::table.cell :label="__('order::attributes.created_at')">
                        {{toPersianNumber($order->created_at_jalali)}}
                    </x-dashboard::table.cell>

                    <td class="data-cell px-4 py-3.5 col-actions" data-label="__('core::attributes.actions')">
                        <div class="flex gap-1">
                            <x-dashboard::buttons.primary-action id="btn-show-order-{{$order->id}}" tag="a"
                                href="{{ route('admin.orders.show', $order) }}" size="sm">
                                <img src="{{ asset('icons/dashboard/vuesax/outline/eye.svg') }}" alt="show" class="w-5" />
                            </x-dashboard::buttons.primary-action>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-slot:body>
    </x-dashboard::table.table>
</section>
