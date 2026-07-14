@push('style')
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

<div class="container mx-auto rtl">
    {{-- {{!- Agent Order Form Section - Main container for order management -}} --}}
    <div class="container mx-auto rtl space-y-6">
        {{-- {{!- Order Form Card - Main form container -}} --}}
        <section class="rounded-2xl bg-white shadow-box p-6 mb-6">
            <!-- عنوان -->
            <div class="">
                <h1 class="text-[18px] md:text-[20px] font-extrabold tracking-tight">
                    @lang('order::attributes.order_code') {{$order->code}}
                </h1>
            </div>
            <!-- نام مشتری -->
            {{-- <x-Order::order.form._customer />

            <!-- جداکننده -->
            <div class="mt-6 border-t border-gray-100"></div> --}}
            @if($order->status == Modules\Order\Enums\OrderStatus::DRAFT->value)
                <livewire:order::product-selector :order="$order" />
            @endif
            {{-- {{!- Price Table: Displays product sizes and pricing -}} --}}
            <!-- Table Container: Handles overflow and scrollability -->
            <livewire:order::products-table :order="$order" />
            @if($order->status == Modules\Order\Enums\OrderStatus::DRAFT->value)
                <div class="relative overflow-x-auto w-full pb-2">
                    <div class="flex flex-col gap-2">
                        <label class="shrink-0 text-[13px] font-semibold text-gray-800 w-full">
                            @lang('order::messages.enter_description')
                        </label>
                        <textarea wire:model="form.description" rows="3"
                            class="w-full h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            {{ $form['description'] }}
                        </textarea>
                    </div>
                </div>
            @endif
            {{-- {{!- Submit Button - Saves the order -}} --}}
            @if($order->status == Modules\Order\Enums\OrderStatus::DRAFT->value)
                <div class="py-2 flex justify-start">
                    <p
                        class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 inset-ring inset-ring-blue-700/10">
                        سفارش تا زمانی که تایید نهایی نشود در پیش نویس شما باقی می ماند.</p>
                </div>
                @if(count($missingRouteProducts))
                    <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700 inset-ring inset-ring-red-600/10">
                        <p class="font-semibold mb-2">محصولات زیر مسیر تولید ندارند و باید ابتدا برایشان مسیر تعریف شود:</p>
                        <ul class="list-disc pr-5 space-y-1">
                            @foreach($missingRouteProducts as $missingProduct)
                                <li>
                                    @if($missingProduct['url'])
                                        <a href="{{ $missingProduct['url'] }}" target="_blank"
                                            class="font-medium text-red-800 underline hover:text-red-900">
                                            {{ $missingProduct['title'] }}
                                        </a>
                                    @else
                                        {{ $missingProduct['title'] }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="py-4 flex justify-start">
                    <x-Core::button wire:click="submitOrder"
                        class="bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72]">
                        @lang('order::attributes.submit_order')
                    </x-Core::button>
                    {{-- Manual/imported orders may also be sent to production once every
                         non-fabric product has a production route; submit-time validation
                         tells the admin which products still need one. --}}
                    @if($order->user_id)
                        <x-Core::button wire:click="confirmFinalApproval"
                            class="bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72] mr-2">
                            @lang('order::attributes.confirm_final_order')
                        </x-Core::button>
                    @endif
                </div>
            @endif
        </section>
    </div>
    <!-- ====== /نمای سفارش نمایندگان ====== -->
</div>
