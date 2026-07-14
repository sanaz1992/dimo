<div class="relative overflow-x-auto w-full pb-2">
    @if(isset($order))
        @can('orders_edit')
            @if($order->status == \Modules\Order\Enums\OrderStatus::DRAFT->value)
                {{-- Seller can only be changed while the order is still a draft. --}}
                <div class="mb-5 print:hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                @lang('order::attributes.seller')
                            </label>
                            <select wire:model="sellerId" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="">@lang('order::attributes.select_seller')</option>
                                @foreach($sellers as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('sellerId')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-end">
                            <x-Core::button wire:click="saveSeller"
                                class="bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72]">
                                {{-- <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg> --}}
                                @lang('core::messages.save')
                            </x-Core::button>
                        </div>
                    </div>
                </div>
            @elseif($sellerId)
                {{-- Once past draft the seller is locked; show it read-only. --}}
                <div class="mb-5 print:hidden">
                    <span class="text-sm font-medium text-gray-700">@lang('order::attributes.seller'):</span>
                    <span class="text-sm text-gray-900">{{ $sellers[$sellerId] ?? '—' }}</span>
                </div>
            @endif
        @endcan
    @endif
    <!-- Shadow to indicate scrollability on mobile -->
    <div
        class="absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-white to-transparent pointer-events-none z-10 md:hidden">
    </div>
    <!-- Outer wrapper handles the rounded border -->
    <div class="rounded-2xl overflow-hidden min-w-max w-full">
        <table class="w-full text-sm print:text-[12px] text-gray-700 border-collapse">
            <thead class="divide-x-4 divide-white">
                <tr class="text-center divide-x-4 divide-white">
                    <th class="bg-amber-100/60 px-4 py-3 print:px-2 print:py-1.5 font-semibold min-w-[64px] whitespace-nowrap border-l-4 border-white">
                        ردیف
                    </th>
                    <th class="bg-amber-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[100px] whitespace-nowrap">
                        کد محصول
                    </th>
                    <th class="bg-amber-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[80px] whitespace-nowrap">
                        تعداد
                    </th>
                    <th class="bg-amber-100/60 px-6 py-3 print:px-2 print:py-1.5 font-semibold min-w-[300px] whitespace-nowrap text-start">
                        نام محصول
                    </th>
                    @if($currentUser->level == Modules\User\Enums\UserLevel::SELLER->value || $currentUser->can('order_price_show'))
                        <th class="bg-amber-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[130px] whitespace-nowrap">
                            قیمت واحد ({{$currency}})
                        </th>
                        <th class="bg-amber-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[150px] whitespace-nowrap">
                            مبلغ نهایی ({{$currency}})
                        </th>
                    @endif
                    @if(!isset($order) || $order->status == Modules\Order\Enums\OrderStatus::DRAFT->value)
                        <th class="bg-amber-100/60 px-3 py-3 print:px-2 print:py-1.5 font-semibold min-w-[100px] whitespace-nowrap">
                            عملیات
                        </th>
                    @endif
                </tr>
            </thead>
            @if(isset($order))
                <tbody class="divide-y-4 divide-white [&_td]:bg-[#F6F6F5] [&_td]:p-3 print:[&_td]:p-1.5">
                    @foreach ($order->items as $item)
                        <tr class="text-center divide-x-4 divide-white">
                            <td>{{$loop->index + 1}}</td>
                            <td>{{$item->product->code}}</td>
                            <td>{{ (float) $item->qty + 0 }}</td>
                            <td class="text-start !px-6 align-middle">
                                <div class="inline-flex flex-col items-start gap-2 text-right max-w-[280px] sm:max-w-[350px] md:max-w-[450px] lg:max-w-[550px] whitespace-normal break-words">
                                    <div class="flex items-center gap-3">
                                        @if($item->product->main_image)
                                            <img src="{{ $item->product->main_image->getThumbnailUrl('small') }}" alt="{{ $item->product->title }}" class="w-10 h-10 rounded-full object-cover border border-black/10 shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-100 border border-black/10 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="font-bold text-gray-800">{{$item->product->title}}</span>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <?php $fabricPrice = 0; ?>
                                        @forelse  ($item->item_fabrics as $itemFabric)
                                            <div class="flex items-center gap-3 text-xs">
                                                {{-- @if(isset($itemFabric->fabric) && $itemFabric->fabric->media) --}}
                                                    <div class="w-10 h-10 rounded-full shadow-sm overflow-hidden border border-blue-100 shrink-0 bg-white">
                                                        <img src="{{$itemFabric->fabric->main_image->getThumbnailUrl('small')}}" alt="Fabric" class="w-full h-full object-cover">
                                                    </div>
                                                {{-- @else
                                                    <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                        </svg>
                                                    </div>
                                                @endif --}}
                                                <div class="flex items-center flex-wrap gap-1.5 text-blue-700 leading-none">
                                                    <span class="font-medium">{{$itemFabric->product_required_fabric->title}}:</span>
                                                    <span>{{$itemFabric->fabric->fabric_model?->title}}</span>
                                                    @if($itemFabric->fabric->color_code)
                                                        <span class="text-blue-300">|</span>
                                                        <span>کد: <span class="font-medium">{{$itemFabric->fabric->color_code}}</span></span>
                                                    @endif
                                                    <span class="text-blue-300">|</span>
                                                    <span class="text-blue-500">({{$itemFabric->qty}} متر)</span>
                                                </div>
                                            </div>
                                            <?php $fabricPrice += $fabrics[$itemFabric->fabric_id]['price'] * $itemFabric->qty; ?>
                                        @empty
                                            @if($item->product->has_fabric)
                                                <div class="flex items-center gap-3 text-xs">
                                                    <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                        </svg>
                                                    </div>
                                                    <span class="font-medium text-blue-700">
                                                        {{__('order::attributes.send_fabric_by_customer')}}
                                                    </span>
                                                </div>
                                            @endif
                                        @endforelse
                                        @if($item->frame_color_id)
                                            <div class="flex items-center gap-3 text-xs">
                                                <div class="w-10 h-10 rounded-full bg-green-50 border border-green-100 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25l7.22-7.22a3.75 3.75 0 015.304 5.304L6.75 21z" />
                                                    </svg>
                                                </div>
                                                <div class="flex items-center gap-1.5 text-green-700 leading-none">
                                                    <span class="font-medium">رنگ چوب:</span>
                                                    <span>{{$item->frame_color->title}}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            @if($currentUser->level == Modules\User\Enums\UserLevel::SELLER->value || $currentUser->can('order_price_show'))
                                <td class="text-center whitespace-nowrap">
                                    @if($order->status == Modules\Order\Enums\OrderStatus::DRAFT->value && $item->status == Modules\Order\Enums\OrderItemStatus::DRAFT->value)
                                        <div x-data="{ words: '' }" x-init="words = window.tomanInWords ? window.tomanInWords(@js($item->price)) : ''">
                                            <div class="flex items-center justify-center gap-1">
                                                <input type="number" min="0" step="1"
                                                    wire:model.defer="prices.{{ $item->id }}"
                                                    x-on:input="words = window.tomanInWords($event.target.value)"
                                                    class="w-28 rounded-lg border-gray-300 text-center text-sm"
                                                    placeholder="{{ __('order::attributes.price') }}" />
                                                <button type="button" wire:click="saveItemPrice({{ $item->id }})"
                                                    class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                                    title="{{ __('core::messages.save') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p x-show="words" x-text="words" class="text-[11px] text-gray-500 mt-1 leading-5"></p>
                                        </div>
                                        @error('prices.'.$item->id)
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <span>{{number_format($item->price - ($fabricPrice / $item->qty))}}</span>
                                    @endif
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    <span>
                                        {{number_format($item->total_price - $fabricPrice)}}
                                        @if($item->custom_frame)
                                            <span
                                                class="inline-flex items-center mt-3 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 inset-ring inset-ring-blue-700/10">
                                                +10% فریم اختصاصی
                                            </span>
                                        @endif
                                    </span>
                                </td>
                            @endif

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
                    @if($showFabricMeterageInOrder)
                        @foreach ($fabrics as $fabric)
                            <tr class="text-center divide-x-4 divide-white">
                                <td>{{$loop->index + 1}}</td>
                                <td>{{$fabric['code']}}</td>
                                <td>{{$fabric['qty']}}</td>
                                <td class="text-center">
                                    {{$fabric['title']}} - {{$fabric['color_code']}}
                                </td>
                                @can('order_price_show')
                                    <td class="text-center whitespace-nowrap">
                                        <span>{{number_format($fabric['price'])}}</span>
                                    </td>
                                    <td class="text-center whitespace-nowrap">
                                        <span>
                                            {{number_format($fabric['price'] * $fabric['qty'])}}
                                        </span>
                                    </td>
                                @endcan
                                @if($order->status == Modules\Order\Enums\OrderStatus::DRAFT->value)
                                    <td class="px-2 whitespace-nowrap">
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            @endif
            @if($currentUser->level == Modules\User\Enums\UserLevel::SELLER->value || $currentUser->can('order_price_show'))
                <tfoot class="border-t-4 border-white">
                    <tr class="divide-x-4 divide-white">
                        <td colspan="4" class="px-4 py-3 print:px-2 print:py-1.5 text-left text-gray-500"></td>

                        <!-- برچسب -->
                        <td class="px-4 py-4 print:px-2 print:py-2 text-center font-semibold bg-[#B67E36] text-white rounded-br-xl">
                            مبلغ نهایی
                        </td>

                        <!-- مبلغ: ادغام دو ستون آخر -->
                        <td colspan="2" class="px-4 py-3 print:px-2 print:py-1.5 text-center font-bold bg-[#F6F6F5] rounded-bl-xl">
                            <span>{{number_format($order?->total_price)}}</span>
                        </td>
                    </tr>
                </tfoot>
            @endif

        </table>
    </div>
</div>

@assets
    <script>
        // Spell out an integer amount (تومان) in Persian words. Built locally — no package needed.
        window.numberToPersianWords = function (num) {
            num = Math.floor(Number(num));
            if (isNaN(num) || num === 0) return '';

            const yekan = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
            const dahYaz = ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'];
            const dahgan = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];
            const sadgan = ['', 'صد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];
            const scales = ['', ' هزار', ' میلیون', ' میلیارد', ' هزار میلیارد'];

            const threeDigit = function (n) {
                const parts = [];
                const sad = Math.floor(n / 100);
                const rem = n % 100;
                if (sad > 0) parts.push(sadgan[sad]);
                if (rem >= 10 && rem <= 19) {
                    parts.push(dahYaz[rem - 10]);
                } else {
                    const d = Math.floor(rem / 10);
                    const y = rem % 10;
                    if (d > 0) parts.push(dahgan[d]);
                    if (y > 0) parts.push(yekan[y]);
                }
                return parts.join(' و ');
            };

            const groups = [];
            let scaleIndex = 0;
            while (num > 0) {
                const three = num % 1000;
                if (three > 0) {
                    groups.unshift(threeDigit(three) + scales[scaleIndex]);
                }
                num = Math.floor(num / 1000);
                scaleIndex++;
            }
            return groups.join(' و ');
        };

        // Convert a raw ریال input to its تومان value spelled out in Persian words.
        window.tomanInWords = function (rialValue) {
            const digits = String(rialValue ?? '').replace(/[^\d]/g, '');
            if (!digits) return '';
            const toman = Math.floor(Number(digits) / 10);
            if (toman === 0) return '';
            return window.numberToPersianWords(toman) + ' تومان';
        };
    </script>
@endassets
