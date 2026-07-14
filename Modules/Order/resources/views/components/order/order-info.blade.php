<section
    class="rounded-2xl bg-white shadow-box print:shadow-none print:border print:border-gray-200 p-4 sm:p-6 print:p-4">
    <!-- عنوان -->
    <div class="text-center sm:text-right">
        <h1 class="text-lg sm:text-xl md:text-2xl print:text-lg font-extrabold">
            @lang('order::attributes.orders_show') : @lang('order::attributes.code')
            <span class="tracking-wider text-emerald-600">{{$order->code}}</span>
            <span
                class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{Modules\Order\Enums\OrderType::colors()[$order->type] }}">
                {{Modules\Order\Enums\OrderType::labels()[$order->type]}}
            </span>
        </h1>
    </div>

    {{-- {{!- Order Info Row - Key order details in a responsive grid -}} --}}
    <div class="mt-4 print:mt-2 grid grid-cols-1 sm:grid-cols-2 @can('order_price_show')
        lg:grid-cols-4 print:grid-cols-4
    @else lg:grid-cols-3 print:grid-cols-3
    @endcan gap-4 print:gap-2 text-sm print:text-xs text-gray-700">
        <x-Order::order.order-info-box :title="__('order::attributes.code')" :value="$order->code" />

        <x-Order::order.order-info-box :title="__('order::attributes.date')" :value="$order->created_at_jalali_date" />

        <x-Order::order.order-info-box :title="__('order::attributes.status')"
            :value="Modules\Order\Enums\OrderStatus::labels()[$order->status]" />

        @can('order_price_show')
            <x-Order::order.order-info-box :title="__('order::attributes.total_cost')"
                :value="number_format($order->total_price / 10)" />
        @endcan

    </div>
    @if($order->seller)
        <div class="mt-4 print:mt-2 flex items-center gap-2 text-sm print:text-xs text-gray-700">
            <span class="text-gray-500">@lang('order::attributes.seller'):</span>
            <span class="font-medium text-gray-900">{{ $order->seller->name }}</span>
        </div>
    @endif
    @if($order->status == Modules\Order\Enums\OrderStatus::CANCELED->value && count($order->histories))
        @php
            $cancellationHistory = $order->histories->where('new_value', Modules\Order\Enums\OrderStatus::CANCELED->value)->first() ?? $order->histories->first();
        @endphp
        <div class="mt-6 print:mt-3">
            <div
                class="relative overflow-hidden rounded-xl border border-red-200/60 bg-gradient-to-r from-red-50/90 to-rose-50/40 py-3 px-4 print:py-2 print:px-3 shadow-sm backdrop-blur-sm transition-all duration-300 hover:shadow-md">
                <!-- Glowing red accent line on the right side (RTL Start) -->
                <div class="absolute inset-y-0 right-0 w-1 bg-gradient-to-b from-red-500 to-rose-600 rounded-r-xl"></div>

                <div class="flex items-center gap-3.5 print:gap-2">
                    <!-- Premium Warning Icon Wrapper -->
                    <div
                        class="flex h-9 w-9 print:h-6 print:w-6 shrink-0 items-center justify-center rounded-lg bg-red-100/80 text-red-600 border border-red-200/50 shadow-inner">
                        <svg class="h-5 w-5 print:h-4 print:w-4 animate-pulse" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 text-right">
                        <p class="text-sm print:text-xs font-semibold text-red-800/90 leading-relaxed">
                            {{ $cancellationHistory?->description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($order->type == Modules\Order\Enums\OrderType::SALE->value)
        <hr class="my-6 print:my-3 border-gray-100" />

        {{-- {{!- Order Summary Section - Split view of order details and customer info -}} --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-2 gap-6 print:gap-4 h-full items-stretch">
            <!-- خلاصه سفارش -->
            @can('order_price_show')
                <div
                    class="w-full lg:border-l lg:border-gray-100 lg:pl-6 print:border-l print:border-gray-100 print:pl-4 flex flex-col h-full">
                    <h3 class="font-bold text-gray-900 mb-4 print:mb-2 text-center sm:text-right print:text-sm">
                        @lang('order::attributes.order_summary')
                    </h3>
                    <dl
                        class="text-sm print:text-xs flex flex-col gap-3 print:gap-1 bg-gray-50 p-4 print:p-2 rounded-xl flex-grow">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">@lang('order::attributes.total_amount_of_products')</dt>
                            <dd class="font-medium text-gray-800">
                                {{number_format($order->total_price / 10)}}
                                <span class="text-xs print:text-[10px] text-gray-400"> {{$currency}}</span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">@lang('order::attributes.shipping_cost')</dt>
                            <dd class="font-medium text-gray-800">
                                0 <span class="text-xs print:text-[10px] text-gray-400"> {{$currency}}</span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500"> @lang('order::attributes.tax')</dt>
                            <dd class="font-medium text-gray-800">
                                0 <span class="text-xs print:text-[10px] text-gray-400"> {{$currency}}</span>
                            </dd>
                        </div>
                        <div class="pt-3 border-t mt-1 flex items-center justify-between">
                            <dt class="text-gray-600 font-semibold">
                                @lang('order::attributes.final_total')
                            </dt>
                            <dd class="text-gray-900 font-extrabold text-lg print:text-sm">
                                {{number_format($order->total_price / 10)}}
                                <span class="text-sm print:text-xs font-normal text-gray-600"> {{$currency}}</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            @endcan

            <!-- اطلاعات مشتری -->
            <div class="w-full lg:pr-6 print:pr-4 flex flex-col h-full">
                <h3 class="font-bold text-gray-900 mb-4 print:mb-2 text-center sm:text-right print:text-sm">
                    اطلاعات مشتری
                </h3>
                <dl
                    class="text-sm print:text-xs flex flex-col gap-3 print:gap-1 bg-gray-50 p-4 print:p-2 rounded-xl flex-grow">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-4 print:gap-2 print:flex-row print:items-center">
                        <dt class="text-gray-500 whitespace-nowrap">@lang('order::attributes.customer_name'):</dt>
                        <dd class="font-medium text-gray-800 text-left sm:text-right">
                            {{$order->user->name}}
                        </dd>
                    </div>
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-4 print:gap-2 print:flex-row print:items-center">
                        <dt class="text-gray-500 whitespace-nowrap">@lang('order::attributes.customer_mobile'):</dt>
                        <dd class="font-medium text-gray-800 dir-ltr">
                            {{$order->user->mobile}}
                        </dd>
                    </div>
                    <div
                        class="flex flex-col sm:flex-row sm:items-start justify-between gap-1 sm:gap-4 print:gap-2 print:flex-row print:items-start">
                        <dt class="text-gray-500 whitespace-nowrap pt-1">@lang('order::attributes.address'):</dt>
                        <dd class="font-medium text-gray-800 text-justify sm:text-right md:max-w-[60%]">
                            {{-- تهران، خیابان انقلاب، خیابان آزادی، کوچه شهید فلانی، پلاک
                            ۱۲۳، طبقه ۲، واحد ۴ --}}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    @endif
    @if ($order->description)
        <hr class="my-6 print:my-3 border-gray-100" />
        <div class="grid grid-cols-1 gap-6 print:gap-3">
            <h3 class="font-bold text-gray-900 mb-4 print:mb-2 text-center sm:text-right print:text-sm">
                @lang('order::attributes.description')
            </h3>
            <dl class="text-sm print:text-xs flex flex-col gap-3 print:gap-1 bg-gray-50 p-4 print:p-2 rounded-xl flex-grow">
                <div class="flex items-center justify-between">
                    <dd class="font-medium text-gray-800">
                        {{$order->description ?? '----'}}
                    </dd>
                </div>
            </dl>
        </div>
    @endif
</section>
