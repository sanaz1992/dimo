@push('styles')
    <style>
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: none;
            padding-right: 0.75rem !important;
        }

        select::-ms-expand {
            display: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@php
    use Modules\Factory\Enums\ProductionOrderItemStepStatus;
@endphp
<div class="container mx-auto rtl ">
    <div class="container mx-auto rtl space-y-6">

        <section class="rounded-2xl bg-white shadow-box overflow-hidden">


            {{-- {{!- Control Panel Section -}} --}}
            <div class="p-4 sm:p-6 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- {{!- Order Code Selector -}} --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            @lang('order::messages.enter_order_code')
                        </label>
                        <div class="relative">
                            {{-- <input wire:model="form.title" type="text" class="w-full pr-10 pl-3 text-right rounded-lg border-[#E7E7E6] py-2.5 bg-white shadow-[0px_1px_2px_0px_rgba(16, 24, 40, 0.05)]"
                                    wire:input="changeQuery($event.target.value)"        placeholder="{{__('product::messages.enter_title')}}" /> --}}
                            <select wire:model="form.order_code" wire:change="changeOrder($event.target.value)"
                                    class="w-full pr-10 pl-3 text-right rounded-lg border-[#E7E7E6] py-2.5 bg-white shadow-[0px_1px_2px_0px_rgba(16, 24, 40, 0.05)]">
                                <option value="">
                                    {{__('order::attributes.select_order')}}
                                </option>
                                @foreach ($orders as $or)
                                    <option value="{{ $or->code }}" {{ $or->code == $order->code ? 'selected' : ''}}>
                                        {{ $or->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- {{!- Product Selector -}} --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            @lang('order::messages.select_product')
                        </label>
                        <div class="relative">
                            <select wire:model="form.product_id" wire:change="changeProduct($event.target.value)"
                                    class="w-full pr-10 pl-3 text-right rounded-lg border-[#E7E7E6] py-2.5 bg-white shadow-[0px_1px_2px_0px_rgba(16, 24, 40, 0.05)]">
                                <option value="">
                                    {{__('order::attributes.select_product')}}
                                </option>
                                @foreach ($order->items as $item)
                                    <option value="{{$item->product->id}}">{{ $item->product->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if(isset($order->id))
            <section class="rounded-2xl bg-white shadow-box overflow-hidden px-4 sm:px-6 py-2 flex flex-col gap-4 ">
                <h2 class="text-lg font-bold">
                    {{__('order::attributes.customer_info')}}
                </h2>
                @if(!empty($order->user))
                    <div class="flex flex-col gap-4 [&>div]:flex [&>div]:flex-row [&>div>h3]:min-w-[200px] [&>div>span]:font-semibold">
                        <div>
                            <h3>
                                {{__('order::attributes.customer_name')}}
                            </h3>
                            <span>
                            {{$order->user->name}}
                        </span>
                        </div>
                        <div>
                            <h3>
                                {{__('order::attributes.customer_mobile')}}
                            </h3>
                            <span>
                            {{$order->user->mobile}}
                        </span>
                        </div>
                        <div>
                            <h3>
                                {{__('order::attributes.customer_code')}}
                            </h3>
                            <span>
                            {{$order->user->unique_code}}
                        </span>
                        </div>
                    </div>
                @else
                    <div>
                        {{__('order::messages.customer_not_found')}}
                    </div>
                @endif
            </section>
            @foreach ($order->items as $item)
                @if(!$selectedProductId || $selectedProductId == $item->product_id)
                    <section class="rounded-2xl bg-white shadow-box overflow-hidden">
                        <div x-data="{ isOpen: true }" class="border-b border-gray-200">
                            <button @click="isOpen = !isOpen"
                                    class="w-full px-4 sm:px-6 py-2 flex items-center justify-between text-left bg-[#3E3E3B] transition-colors">
                                <div class="flex items-center gap-3">
                                    <div
                                            class="w-10 h-10 rounded-2xl bg-gray-200 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                        <img src="{{ $item->product->main_image?->getThumbnailUrl('small')  }}"
                                             alt="{{$item->product->title}}" class="w-full h-full object-cover"/>
                                    </div>
                                    <h3 class="text-xs sm:text-sm md:text-base font-bold text-white">
                                        {{$item->product->title}}
                                            <?php $canOpen = false; ?>
                                        @if($item->status == Modules\Order\Enums\OrderItemStatus::APPROVED->value)
                                            محصول تایید شده و منتظر ارسال به ایستگاه میباشد
                                        @elseif($item->status == Modules\Order\Enums\OrderItemStatus::PRODUCED->value)
                                            محصول تکمیل شده و در انبار میباشد
                                        @else
                                                <?php $canOpen = true; ?>
                                            <span class="bg-blue-50 text-blue-700 inset-ring-blue-700/10 inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap mr-1">
                                                       در حال تولید
                                                   </span>
                                        @endif

                                    </h3>
                                </div>
                                @if($canOpen)
                                    <svg class="w-5 h-5 text-white transition-transform duration-200"
                                         :class="{ 'rotate-180': isOpen }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 9l-7 7-7-7"/>
                                    </svg>
                                @endif
                            </button>
                            @if($canOpen)
                                    <?php $itemProductionGroups = $item->production_order_items->groupBy('group_name'); ?>

                                <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform translate-y-0"
                                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                                     class="px-4 sm:px-6 pb-4 sm:pb-6">

                                    @foreach ($itemProductionGroups as $groupName=>$productionOrderItems)
                                        <div class="flex flex-col gap-3">
                                            <span class="px-3 mt-3 p-1 border border-black/10 text-sm w-fit rounded-lg">@lang('factory::attributes.production_code') : {{ $groupName }}</span>
                                                <?php $productionOrderItemSortGroups = $productionOrderItems->groupBy('sort');
                                                $showHallProduction = false; ?>
                                            @foreach($productionOrderItemSortGroups as $sort=>$productionOrderItemSortGroup)
                                                @continue($productionOrderItemSortGroup->where('status','done')->count()==$productionOrderItemSortGroup->count())
                                                @if(!$showHallProduction)
                                                        <?php $showHallProduction = true; ?>
                                                    @foreach ($productionOrderItemSortGroup as $productionOrderItem)
                                                        {{-- Static Product Process 1 --}}
                                                        <div>
                                                            <div
                                                                    class="flex flex-col md:flex-row items-start md:items-center gap-0 sm:gap-4 md:gap-3">
                                                                <h3 class="font-semibold text-gray-900 min-w-0 w-[150px]">
                                                                    {{ $productionOrderItem->product_hall_difination->hall->production_processes->first()->title }}
                                                                </h3>
                                                                {{-- Progress Timeline --}}
                                                                <div
                                                                        class="flex-1 w-full md:w-auto min-w-0 overflow-x-auto pb-4 -mr-3 sm:-mr-0 pr-6 md:pr-12 ">
                                                                    <div class="relative py-6"
                                                                         style="min-width: max-content">
                                                                        {{-- Progress Steps Container with proper spacing --}}
                                                                        <div class="relative flex items-center">
                                                                            @forelse ($productionOrderItem->steps as $index=>$step)
                                                                                {{-- Step 1: Completed --}}
                                                                                <div class="relative flex items-center min-w-[60px] sm:min-w-[70px]"
                                                                                     style="flex: 1 1 0">
                                                                                    <div
                                                                                            class="flex flex-col items-center relative z-20 w-9 sm:w-10 flex-shrink-0">
                                                                                        <div class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center transition-all relative  {{Modules\Factory\Enums\ProductionOrderItemStepStatus::node_colors()[$step->status] }}"
                                                                                             style="box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);">
                                                                                            {!! Modules\Factory\Enums\ProductionOrderItemStepStatus::node_icons()[$step->status] !!}
                                                                                        </div>
                                                                                        {{-- Step Label --}}
                                                                                        <span
                                                                                                class="absolute left-1/2 -translate-x-1/2 text-[10px] sm:text-xs text-center leading-tight px-0.5 sm:px-1 whitespace-nowrap top-9 sm:top-10 md:top-11 max-w-[100px] sm:max-w-[120px] overflow-hidden truncate">
                                                                                                {{$step->hall_process_step->title}}
                                                                                            </span>
                                                                                    </div>
                                                                                    {{-- Connection Line (only if not last) --}}
                                                                                    @if($index < $productionOrderItem->steps->count() - 1)
                                                                                        <div class="relative flex-1 z-10 min-w-[60px] sm:min-w-[80px] md:min-w-[90px]"
                                                                                             style="height: 0">
                                                                                            {{-- Background line --}}
                                                                                            <div
                                                                                                    class="absolute right-0 left-0 h-0.5 bg-gray-300 rounded-full top-1/2 -translate-y-1/2">
                                                                                            </div>
                                                                                            {{-- Progress line --}}
                                                                                            @if($step->histories->count())
                                                                                                <div class="absolute top-1/2 -translate-y-full mb-1 text-xs text-gray-700 text-center"
                                                                                                     style="
                                                                                                                                                                    right: 4px;
                                                                                                                                                                    left: 4px;
                                                                                                                                                                    transform: translateY(-100%);
                                                                                                                                                                    line-height: 1.4;
                                                                                                                                                                    max-height: 2.8em;
                                                                                                                                                                    overflow: hidden;
                                                                                                                                                                    display: -webkit-box;
                                                                                                                                                                    -webkit-line-clamp: 2;
                                                                                                                                                                    -webkit-box-orient: vertical;
                                                                                                                                                                ">
                                                                                                    @foreach ($step->histories as $history)
                                                                                                        @if($history->description != null)
                                                                                                            <span title="{{ $history->description }}">
                                                                                                                {{$history->description}}
                                                                                                            </span>
                                                                                                        @endif
                                                                                                    @endforeach
                                                                                                </div>
                                                                                            @endif
                                                                                            <div
                                                                                                    class="absolute right-0 h-0.5 rounded-full transition-all duration-300 top-1/2 -translate-y-1/2  {{Modules\Factory\Enums\ProductionOrderItemStepStatus::node_connection_colors()[$step->status] }} w-full">
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @empty
                                                                                در انتظار ارسال به ایستگاه
                                                                            @endforelse
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                            @if(!$showHallProduction)
                                                تولید محصول تمام شده
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>
                @endif
            @endforeach
        @endif
    </div>
</div>
