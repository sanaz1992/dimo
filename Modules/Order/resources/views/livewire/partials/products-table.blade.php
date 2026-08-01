<article class="metric metric-d2 mt-3" style="overflow: scroll">
    <!-- Shadow to indicate scrollability on mobile -->
    <div
        class="absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-white to-transparent pointer-events-none z-10 md:hidden">
    </div>
    <!-- Outer wrapper handles the rounded border -->
    <div class="rounded-2xl overflow-hidden min-w-max w-full">
        <table class="w-full text-sm print:text-[12px] text-gray-700 border-collapse">
            <thead class="divide-x-4 divide-white">
                <tr class="text-center divide-x-4 divide-white">
                    <th class="bg-blue-100/60 px-4 py-3 print:px-2 print:py-1.5 font-semibold min-w-[64px] whitespace-nowrap border-l-4 border-white">
                        ردیف
                    </th>
                    <th class="bg-blue-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[100px] whitespace-nowrap">
                        کد محصول
                    </th>
                    <th class="bg-blue-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[80px] whitespace-nowrap">
                        تعداد
                    </th>
                    <th class="bg-blue-100/60 px-6 py-3 print:px-2 print:py-1.5 font-semibold min-w-[300px] whitespace-nowrap text-start">
                        نام محصول
                    </th>
                        <th class="bg-blue-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[130px] whitespace-nowrap">
                            قیمت واحد ({{$currency}})
                        </th>
                        <th class="bg-blue-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[130px] whitespace-nowrap">
                            تخفیف ({{$currency}})
                        </th>
                        <th class="bg-blue-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[150px] whitespace-nowrap">
                            مبلغ نهایی ({{$currency}})
                        </th>
                    @if(!isset($order) || $order->status == Modules\Order\Enums\OrderStatus::DRAFT->value)
                        <th class="bg-blue-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[100px] whitespace-nowrap">
                            عملیات
                        </th>
                    @endif
                </tr>
            </thead>
            @if(isset($order))
                <tbody class="divide-y-4 divide-white [&_td]:bg-[#F6F6F5] [&_td]:p-3 print:[&_td]:p-1.5">
                    @foreach ($order->items as $item)
                        <tr class="text-center divide-x-4 divide-white">
                            <td>{{toPersianNumber($loop->index + 1)}}</td>
                            <td>{{toPersianNumber($item->product_sku->sku)}}</td>
                            <td>{{toPersianNumber( $item->quantity ) }}</td>
                            <td class="text-start !px-6 align-middle">
                                <div class="inline-flex flex-col items-start gap-2 text-right max-w-[280px] sm:max-w-[350px] md:max-w-[450px] lg:max-w-[550px] whitespace-normal break-words">
                                    <div class="flex items-center gap-3">
                                        @if($item->product_sku->product->main_image)
                                            <img src="{{ $item->product_sku->product->main_image->getThumbnailUrl('small') }}" alt="{{ $item->product_sku->product->title }}" class="w-10 h-10 rounded-full object-cover border border-black/10 shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-100 border border-black/10 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="font-bold text-gray-800">{{$item->product_sku->product->name}}</span>
                                    </div>
                                    <div class="flex flex-col gap-2">

                                    </div>
                                </div>

                                <td class="text-center whitespace-nowrap">
                                        <span>{{formatPrice($item->price)}}</span>
                                </td>
                                <td class="text-center whitespace-nowrap">
                                        <span>{{formatPrice($item->discount)}}</span>
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    <span>{{formatPrice($item->total )}}</span>
                                </td>

                            @if($order->status == Modules\Order\Enums\OrderStatus::DRAFT->value)
                                <td class="px-2 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        @if($item->status == Modules\Order\Enums\OrderItemStatus::DRAFT->value)
                                            <!-- Delete Button -->
                                            <button class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="حذف"
                                                wire:click="deleteOrderItem({{$item->id}})">
                                                <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/trash.svg') }}" alt=""
                                                    class="w-5 h-5" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                </tbody>
            @endif
                <tfoot class="border-t-4 border-white">
                    <tr class="divide-x-4 divide-white">
                        <td colspan="4" class="px-4 py-3 print:px-2 print:py-1.5 text-left text-gray-500"></td>

                        <!-- برچسب -->
                        <td class="px-4 py-4 print:px-2 print:py-2 text-center font-semibold bg-indigo-600 text-white rounded-br-xl">
                            مبلغ نهایی
                        </td>

                        <!-- مبلغ: ادغام دو ستون آخر -->
                        <td colspan="2" class="px-4 py-3 print:px-2 print:py-1.5 text-center font-bold bg-[#F6F6F5] rounded-bl-xl">
                            <span>{{formatPrice($order?->total_amount)}}</span>
                        </td>
                    </tr>
                </tfoot>

        </table>
    </div>
</article>

