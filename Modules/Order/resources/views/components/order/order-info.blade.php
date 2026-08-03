<article class="metric metric-d2" data-searchable="">
    <div class="relative z-[1] flex justify-between gap-3">
        <p class="text-[12px] font-medium text-ink-faint">
            @lang('order::attributes.order_number') :
            {{toPersianNumber($order->order_number)}}
        </p>
    </div>
    <div class="relative z-[1] flex flex-wrap gap-2 mt-3 mb-3">
        <x-Order::order.order-info-box :title="__('order::attributes.created_at')"
            :value="toPersianNumber($order->created_at_jalali)" />

        <x-Order::order.order-info-box :title="__('order::attributes.status')" :color="$order->status->color()" :value="$order->status->label()" />

        <x-Order::order.order-info-box :title="__('order::attributes.total_amount')"
            :value="formatPrice($order->total_amount) . ' ' . $currency" />

    </div>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2 anim-stagger">
        <article class="panel p-5 anim-fade-up">
            <h3 class="relative z-[1] mb-4 text-base font-bold text-ink"> @lang('order::attributes.order_summary')</h3>
            <div class="relative z-[1] space-y-3">
                <div class="row">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.total_amount_of_products')</span>
                    <span class="float-left text-[12px] text-ink-faint"> {{formatPrice($order->total_amount)}}
                        {{ $currency }}</span>
                </div>
                <div class="row">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.shipping_cost')</span>
                    <span class="float-left text-[12px] text-ink-faint"> {{formatPrice($order->shipping_cost)}}
                        {{ $currency }}</span>
                </div>
                <div class="row">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.tax')</span>
                    <span class="float-left text-[12px] text-ink-faint">{{formatPrice(0)}} {{ $currency }}</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="--w:100%;--c:#2563eb"></div>
                </div>
                <div class="row">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.final_total')</span>
                    <span class="float-left text-[12px] text-ink-faint"> {{formatPrice($order->total_amount)}}
                        {{ $currency }}</span>
                </div>
            </div>
        </article>

        <article class="panel p-5 anim-fade-up" style="animation-delay:.08s">
            <h3 class="relative z-[1] mb-4 text-base font-bold text-ink"> @lang('order::attributes.customer_info')</h3>
            <div class="relative z-[1] space-y-3">
                <div class="row">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.customer_name')</span>
                    <span class="float-left text-[12px] text-ink-faint"> {{$order->user->name}}</span>
                </div>
                <div class="row">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.customer_mobile')</span>
                    <span class="float-left text-[12px] text-ink-faint"> {{toPersianNumber($order->user->mobile)}}</span>
                </div>
                <div class="row">
                    <span class="block text-[12px] text-ink-faint">@lang('order::attributes.address')</span>
                    <span class="block float-left text-[12px] text-ink-faint">
                        {{$order->address->city->province->name}} ,
                        {{$order->address->city->name}} ,
                        {{toPersianNumber($order->address->address)}},
                        @lang('order::attributes.postal_code') : {{toPersianNumber($order->address->postal_code)}}
                    </span>
                </div>
                <div class="row" style="margin-top: 25px;">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.receiver_name')</span>
                    <span class="float-left text-[12px] text-ink-faint"> {{$order->address->receiver_name}}</span>
                </div>
                <div class="row">
                    <span class="text-[12px] text-ink-faint">@lang('order::attributes.receiver_mobile')</span>
                    <span class="float-left text-[12px] text-ink-faint"> {{toPersianNumber($order->address->receiver_mobile)}}</span>
                </div>
            </div>
        </article>

        @if ($order->description)
            <article class="panel p-5 anim-fade-up" style="animation-delay:.08s">
                <h3 class="relative z-[1] mb-4 text-base font-bold text-ink"> @lang('order::attributes.description')</h3>
                <div class="relative z-[1] space-y-3">
                    <span class="float-left text-[12px] text-ink-faint"> {{$order->description ?? '----'}}</span>
                </div>
            </article>
        @endif
    </section>
</article>
