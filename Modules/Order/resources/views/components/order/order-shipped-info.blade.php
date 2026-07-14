@if($order->shipment_items->count())
    <div class="flex items-center gap-1 px-6 pt-5 pb-1 text-sm">
        <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/truck-fast.svg') }}" alt="" class="w-5 h-5" />
        <span class="font-medium">@lang('order::attributes.loading_code') :</span>
        <span class="font-semibold text-[#A0652E]">
            {{$order->shipment_items->pluck('shipment.loading_number')->filter()->unique()->implode(' , ')}}
        </span>
    </div>

    <div class="flex flex-col md:flex-row gap-4 md:gap-0 md:items-center justify-between text-sm px-6 mt-4 md:mt-0">

        <div class="text-gray-500">
            <?php    $lastOrderItem = $order->items->whereIn('status', [Modules\Order\Enums\OrderItemStatus::SHIPPED->value, Modules\Order\Enums\OrderItemStatus::DELIVERED->value])->sortByDesc('updated_at')->first();  ?>
            @if($lastOrderItem->status == Modules\Order\Enums\OrderItemStatus::SHIPPED->value)
                @lang('order::attributes.posted_on'):
            @else
                @lang('order::attributes.delivered_on'):
            @endif
            <span class="font-medium text-gray-700">
                {{$lastOrderItem->updated_at_jalali}}
            </span>
        </div>

        {{-- {{!- Progress Bar - Visual indicator of order status -}} --}}
        <div class="md:px-6 pb-4 md:w-[50%] xl:w-[30%]">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                <span class="font-medium">{{Modules\Order\Enums\OrderStatus::labels()[$order->status] }}</span>
                {{-- <span>مرحله بعد:
                    <span class="font-semibold text-gray-700">رنگ</span></span> --}}
            </div>
            <div class="h-2 rounded-full bg-gray-100 overflow-hidden w-full flex justify-end">
                <div class="h-full rounded-full transition-all"
                    style="width: {{$doneSteps * 100 / $totalSteps}}%; background-color: #3570DB"></div>
            </div>
        </div>
    </div>
@endif