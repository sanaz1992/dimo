<section>

    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">
        <div class="flex flex-col gap-4 md:flex-row justify-between md:items-center">
            <h2 class="font-semibold text-lg md:text-xl mb-4 md:mb-6">
                {{__('order::attributes.order_list')}}
            </h2>
            <div class="flex items-center gap-4">

                <a href="{{route('seller.orders.create')}}"
                    class="bg-[#3E3E3B] flex items-center gap-2 px-4 py-2 rounded-xl text-white focus:outline-none font-bold">
                    <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5" />
                    <span class="">@lang("order::attributes.new_order")</span>
                </a>
            </div>
        </div>
        <!-- نوار تب‌ها -->

        <!-- محتوای تب‌ها -->
        <div class="tab-content">
            <!-- تب انبار اولیه -->
            <div class="relative">
                <div class="rounded-xl border border-black/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-[800px] w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.row')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.code')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.date')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.customer_name')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.order_qty')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.city')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.status')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($orders as $key => $order)
                                    <tr class="hover:bg-gray-50 {{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}">
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$loop->index + 1}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            @if ($order->status == \Modules\Order\Enums\OrderStatus::DRAFT->value)
                                                <a href="{{route('seller.orders.edit', $order)}}"
                                                    class="text-blue-500 hover:text-blue-700">
                                                    {{$order->code}}
                                                </a>
                                            @else
                                                <a href="{{route('seller.orders.show', $order)}}"
                                                    class="text-blue-500 hover:text-blue-700">
                                                    {{$order->code}}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$order->created_at_jalali_date}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$order?->user?->name}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$order->items->sum('qty')}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$order->seller?->address?->city?->name ?? '-'}}
                                        </td>
                                        <td>
                                            <span
                                                class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{Modules\Order\Enums\OrderStatus::colors()[$order->status] }}">
                                                {{Modules\Order\Enums\OrderStatus::labels()[$order->status] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            <div class="flex justify-end"
                                                x-data="{ showActions: false, menuStyle: '', updatePos(el) { const r = el.getBoundingClientRect(); this.menuStyle = `position:fixed; top:${r.bottom + 8}px; left:${r.left}px;`; } }">
                                                <button
                                                    @click="showActions = !showActions; if (showActions) updatePos($el);"
                                                    @click.away="showActions = false" @resize.window="showActions = false"
                                                    @scroll.window="showActions = false"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                                <!-- منوی عملیات -->
                                                <template x-teleport="body">
                                                    <div x-show="showActions" @click.away="showActions = false"
                                                        :style="menuStyle"
                                                        class="w-48 bg-white rounded-lg shadow-hard-sm z-50 border border-gray-200">
                                                        <div class="">
                                                            @if ($order->status == \Modules\Order\Enums\OrderStatus::DRAFT->value)
                                                                <a href="{{route('seller.orders.edit', $order)}}"
                                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <i class="fas fa-edit ml-2"></i>
                                                                    {{__('order::attributes.edit') }}
                                                                </a>
                                                            @else
                                                                <a href="{{route('seller.orders.show', $order)}}"
                                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <i class="fas fa-eye ml-2"></i>
                                                                    {{__('order::attributes.show') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{__('core::messages.without_item')}}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{$orders->links('Core::pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
