@php
    $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class);
    $showFabricMeterageInOrder = $settingHelper->setting('show_fabric_meterage_in_order')->value;
    $currency = $settingHelper->currencyLabel();
    $currentUser = auth()->user();
    
    $fabrics = [];
    if (isset($order)) {
        foreach ($order->items as $item) {
            foreach ($item->item_fabrics as $itemFabric) {
                $fabricId = $itemFabric->fabric_id;
                if (!isset($fabrics[$fabricId])) {
                    $fabrics[$fabricId] = [
                        'code' => $itemFabric->fabric->code,
                        'title' => $itemFabric->fabric->fabric_model?->title,
                        'color_code' => $itemFabric->fabric->color_code,
                        'qty' => 0,
                        'price' => $itemFabric->fabric_price
                    ];
                }
                $fabrics[$fabricId]['qty'] += $itemFabric->qty;
            }
        }
    }
@endphp

<div id="items-table-wrapper" class="w-full mt-8 print:mt-6">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm print:shadow-none print:border-gray-300 overflow-hidden">
        <table id="items-table" class="w-full text-sm print:text-[11px] text-gray-800 border-collapse table-fixed">
            <thead class="bg-gray-50/80 border-b border-gray-200 print:bg-gray-100/60 print:border-gray-300">
                <tr class="text-center text-gray-500 font-medium">
                    <th class="w-12 px-2 py-4 print:py-2.5">ردیف</th>
                    <th class="w-24 px-2 py-4 print:py-2.5">کد کالا</th>
                    <th class="px-4 py-4 print:py-2.5 text-right">شرح کالا / جزئیات</th>
                    <th class="w-16 px-2 py-4 print:py-2.5">تعداد</th>
                    @if($currentUser->level == \Modules\User\Enums\UserLevel::SELLER->value || $currentUser->can('order_price_show'))
                        <th class="w-28 px-2 py-4 print:py-2.5">فی ({{$currency}})</th>
                        <th class="w-32 px-2 py-4 print:py-2.5">مبلغ کل ({{$currency}})</th>
                    @endif
                </tr>
            </thead>
            @if(isset($order))
                <tbody class="divide-y divide-gray-100 print:divide-gray-200">
                    @foreach ($order->items as $item)
                        @php
                            $fabricPrice = 0;
                            foreach ($item->item_fabrics as $itemFabric) {
                                $fabricPrice += $fabrics[$itemFabric->fabric_id]['price'] * $itemFabric->qty;
                            }
                        @endphp
                        <tr class="text-center hover:bg-gray-50/50 transition-colors" style="page-break-inside: avoid;">
                            <td class="px-2 py-5 print:py-3 text-gray-400 align-top">{{$loop->index + 1}}</td>
                            <td class="px-2 py-5 print:py-3 font-medium text-gray-700 tracking-wide">{{$item->product->code}}</td>
                            <td class="px-4 py-5 print:py-3 text-right align-top">
                                <div class="font-bold text-gray-900 text-base print:text-sm mb-1.5">{{$item->product->title}}</div>
                                
                                @if($item->item_fabrics->count() > 0)
                                    <div class="space-y-1.5 mt-2">
                                        @foreach ($item->item_fabrics as $itemFabric)
                                            <div class="flex items-center flex-wrap gap-1.5 text-xs print:text-[10px] text-gray-500 bg-gray-50 print:bg-transparent rounded-md px-2 py-1 print:p-0 w-fit">
                                                <span class="w-1 h-1 rounded-full bg-gray-300 print:bg-gray-400"></span>
                                                <span class="font-medium text-gray-700">{{$itemFabric->product_required_fabric->title}}:</span>
                                                <span class="text-gray-600">{{$itemFabric->fabric->fabric_model?->title}}</span>
                                                @if($itemFabric->fabric->color_code)
                                                    <span class="text-gray-300 mx-0.5">|</span>
                                                    <span>کد: <span class="text-gray-700 font-medium">{{$itemFabric->fabric->color_code}}</span></span>
                                                @endif
                                                <span class="text-gray-300 mx-0.5">|</span>
                                                <span class="text-gray-600">{{$itemFabric->qty}} متر</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($item->product->has_fabric)
                                    <div class="mt-2 flex items-center gap-1.5 text-xs print:text-[10px] text-gray-500 bg-gray-50 print:bg-transparent rounded-md px-2 py-1 print:p-0 w-fit">
                                        <span class="w-1 h-1 rounded-full bg-gray-300 print:bg-gray-400"></span>
                                        <span class="font-medium">{{__('order::attributes.send_fabric_by_customer')}}</span>
                                    </div>
                                @endif

                                @if($item->frame_color_id)
                                    <div class="mt-1.5 flex items-center gap-1.5 text-xs print:text-[10px] text-gray-500 bg-gray-50 print:bg-transparent rounded-md px-2 py-1 print:p-0 w-fit">
                                        <span class="w-1 h-1 rounded-full bg-gray-300 print:bg-gray-400"></span>
                                        <span class="font-medium text-gray-700">رنگ چوب:</span>
                                        <span class="text-gray-600">{{$item->frame_color->title}}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-2 py-5 print:py-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 print:bg-transparent font-semibold text-gray-700 print:text-gray-900">
                                    {{$item->qty}}
                                </span>
                            </td>
                            
                            @if($currentUser->level == \Modules\User\Enums\UserLevel::SELLER->value || $currentUser->can('order_price_show'))
                                <td class="px-2 py-5 print:py-3 text-center whitespace-nowrap text-gray-600">
                                    {{number_format($item->price - ($fabricPrice / $item->qty))}}
                                </td>
                                <td class="px-2 py-5 print:py-3 text-center whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">
                                        {{number_format($item->total_price - $fabricPrice)}}
                                    </div>
                                    @if($item->custom_frame)
                                        <div class="mt-1 text-[10px] text-gray-400 font-medium">
                                            +10% فریم اختصاصی
                                        </div>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    @if($showFabricMeterageInOrder)
                        @foreach ($fabrics as $fabric)
                            <tr class="text-center bg-gray-50/30 print:bg-gray-50/50 hover:bg-gray-50/80 transition-colors" style="page-break-inside: avoid;">
                                <td class="px-2 py-4 print:py-3 text-gray-400 align-top">{{$order->items->count() + $loop->index + 1}}</td>
                                <td class="px-2 py-4 print:py-3 font-medium text-gray-600">{{$fabric['code']}}</td>
                                <td class="px-4 py-4 print:py-3 text-right">
                                    <div class="font-medium text-gray-800">
                                        {{$fabric['title']}} - {{$fabric['color_code']}}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1">متراژ پارچه</div>
                                </td>
                                <td class="px-2 py-4 print:py-3">
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-md bg-gray-100 print:bg-transparent font-medium text-gray-600 text-xs">
                                        {{$fabric['qty']}} متر
                                    </span>
                                </td>
                                
                                @can('order_price_show')
                                    <td class="px-2 py-4 print:py-3 text-center whitespace-nowrap text-gray-500">
                                        {{number_format($fabric['price'])}}
                                    </td>
                                    <td class="px-2 py-4 print:py-3 text-center whitespace-nowrap font-medium text-gray-700">
                                        {{number_format($fabric['price'] * $fabric['qty'])}}
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            @endif

            @if($currentUser->level == \Modules\User\Enums\UserLevel::SELLER->value || $currentUser->can('order_price_show'))
                <tfoot class="bg-gray-50/80 print:bg-gray-100/60 border-t border-gray-200 print:border-gray-300">
                    <tr>
                        <td colspan="4" class="px-6 py-5 print:py-4 text-left font-medium text-gray-500">
                            مبلغ نهایی فاکتور:
                        </td>
                        <td colspan="2" class="px-6 py-5 print:py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <span class="font-bold text-xl print:text-lg text-gray-900">{{number_format($order?->total_price)}}</span>
                                <span class="text-xs text-gray-500 font-medium">{{$currency}}</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
