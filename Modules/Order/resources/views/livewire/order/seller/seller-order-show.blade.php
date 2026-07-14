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

<div class="container mx-auto rtl ">
    {{-- {{!- Agent Order Form Section - Main container for order management -}} --}}
    <div class="container mx-auto rtl space-y-6">
        <x-Order::order.order-info :order="$order" />

        <section class="rounded-2xl bg-white shadow-box p-0 overflow-hidden">
            <x-Order::order.order-shipped-info :order="$order" :doneSteps="$doneSteps" :totalSteps="$totalSteps" />

            <!-- Divider -->

            <hr class="border-gray-200" />

            <div class="w-full flex flex-col" x-data="{ itemsOpen: true }">
                <button @click="itemsOpen = !itemsOpen"
                    class="flex items-center gap-2 px-6 py-6 text-gray-500 hover:text-gray-700 transition-colors w-full justify-between">
                    <span class="font-semibold text-gray-800">
                        @lang('order::attributes.all_order_items') <span class="text-gray-500">({{
    $order->items->count() }})</span>
                    </span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': itemsOpen }" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.17l3.71-2.94a.75.75 0 111.04 1.08l-4.25 3.37a.75.75 0 01-.94 0L5.21 8.31a.75.75 0 01.02-1.1z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- لیست آیتم‌ها -->
                <div x-show="itemsOpen" x-collapse class="divide-y divide-gray-100">
                    @foreach ($order->items as $item)
                        <div class="px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <img src="{{ $item->product->main_image?->getThumbnailUrl('small') }}"
                                    class="h-16 w-24 rounded-lg object-cover ring-1 ring-gray-100" alt="" />
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm text-gray-600 flex-1">
                                    <div class="flex flex-col gap-4 md:col-span-3">
                                        <h2 class="text-[16px] font-semibold">{{ $item->product->title }}</h2>
                                        <div class="flex flex-wrap sm:items-center gap-4 sm:gap-8">
                                            <div class="flex items-center gap-2">
                                                @if($item->frame_color_id)
                                                    <span class="text-gray-500"> @lang('order::attributes.frame_color') :</span>
                                                    <span class="inline-flex items-center gap-1">
                                                        <span class="font-medium text-gray-800">
                                                            {{$item->frame_color->title}}
                                                        </span>
                                                        <span class="h-3 w-3 rounded-full ring-1 ring-gray-200"
                                                            :style="`background:${{$item->frame_color->code}}`"></span>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <?php    $fabricPrice = 0; ?>
                                                @forelse  ($item->item_fabrics as $itemFabric)
                                                    <span class="text-gray-500"> {{$itemFabric->product_required_fabric->title}}
                                                        :</span>
                                                    <span class="inline-flex items-center gap-1">
                                                        <span class="font-medium text-gray-800">
                                                            {{$itemFabric->fabric->title}}
                                                            {{-- ({{$itemFabric->qty}} متر) --}}
                                                        </span>
                                                    </span>
                                                    <span class="text-gray-500"> رنگ :</span>
                                                    <span class="inline-flex items-center gap-1">
                                                        <span class="font-medium text-gray-800">
                                                            {{$itemFabric->color->title}}
                                                        </span>
                                                    </span>
                                                    <?php        /* $fabricPrice += $fabrics[$itemFabric->fabric_id]['price'] * $itemFabric->qty; */ ?>
                                                @empty
                                                    @if($item->product->has_fabric)
                                                        <span class="text-gray-500">
                                                            {{__('order::attributes.send_fabric_by_customer')}}
                                                        </span>
                                                    @endif
                                                @endforelse

                                            </div>
                                            <div>
                                                {{-- <span class="text-gray-500">@lang('order::attributes.fabrics'):</span>
                                                --}}
                                                <!--  <span class="font-medium text-gray-800" x-text="item.wood"></span> -->
                                                {{-- @forelse ($item->item_fabrics as $itemFabric)
                                                <span
                                                    class="inline-flex items-center mt-1 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 inset-ring inset-ring-blue-700/10">
                                                    {{$itemFabric->product_required_fabric->title}} :
                                                    {{$itemFabric->fabric->title}}
                                                    ({{$itemFabric->qty}} متر)
                                                    رنگ : {{$itemFabric->color->title}}
                                                </span>
                                                @empty
                                                @if($item->product->has_fabric)
                                                <span
                                                    class="inline-flex items-center mt-1 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 inset-ring inset-ring-blue-700/10">
                                                    @lang('order::attributes.send_fabric_by_customer')
                                                </span>
                                                @endif
                                                @endforelse --}}
                                            </div>
                                            <div>
                                                <span class="text-gray-500"> @lang('order::attributes.qty'):</span>
                                                <span class="font-medium text-gray-800">{{$item->qty}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 md:justify-end font-extrabold text-gray-900 col-span-1 mt-2 md:mt-0">
                                        <span> {{number_format($item->total_price / 10) }}</span>
                                        <span class="text-gray-500 font-normal">{{$currency}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="py-4 px-6 flex justify-start border-t border-gray-100" x-data="{
                printing: false,
                printInvoice(url) {
                    this.printing = true;
                    let iframe = document.getElementById('print-iframe');
                    if (!iframe) {
                        iframe = document.createElement('iframe');
                        iframe.id = 'print-iframe';
                        iframe.style.position = 'fixed';
                        iframe.style.right = '0';
                        iframe.style.bottom = '0';
                        iframe.style.width = '0';
                        iframe.style.height = '0';
                        iframe.style.border = '0';
                        document.body.appendChild(iframe);
                    }
                    iframe.onload = () => {
                        this.printing = false;
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    };
                    iframe.src = url;
                }
            }">
                <button type="button" @click="printInvoice('{{ route('seller.orders.invoice', $order) }}')"
                    class="rounded-lg px-4 py-2 text-sm bg-blue-600 font-semibold text-white hover:bg-blue-700 mr-2 flex items-center gap-2 transition-all shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] hover:-translate-y-0.5">
                    <svg x-show="!printing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <svg x-show="printing" style="display: none;" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="printing ? 'در حال آماده‌سازی...' : 'چاپ فاکتور'">چاپ فاکتور</span>
                </button>
            </div>
        </section>

    </div>
    <!-- ====== /نمای سفارش نمایندگان ====== -->
</div>
