<div>
    <!-- Top Boxes -->
    <section>
        <div>
            <x-dashboard-top-boxes panel="seller" />
        </div>
    </section>

    <!-- Dashboard Orders -->
    <section class="bg-white p-4 md:p-6 rounded-xl shadow-box mt-6 overflow-x-auto">
        <h2 class="font-semibold text-lg md:text-xl mb-4 md:mb-6">
            @lang('dashboard::attributes.collection_orders')
        </h2>
        <div>
            <div class="overflow-x-auto">
                <div class="overflow-hidden rounded-xl border border-black/10">
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
                                        @lang('order::attributes.status')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($orders as $key => $order)
                                    <tr class="{{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}">
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$loop->index + 1}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">

                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$order->created_at_jalali_date}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$order->user->name}}
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            {{$order->items->sum('qty')}}
                                        </td>
                                        <td>
                                            <span
                                                class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap
                                                {{-- {{Modules\Order\Enums\OrderStatus::colors()[$order->status] }} --}}
                                                ">
                                                {{-- {{Modules\Order\Enums\OrderStatus::labels()[$order->status] }} --}}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                            <div class="flex justify-end relative" x-data="{ showActions: false }">
                                                <button @click="showActions = !showActions"
                                                    @click.away="showActions = false"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                                <!-- منوی عملیات -->
                                                <div x-show="showActions" @click.away="showActions = false"
                                                    class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-hard-sm z-50 border border-gray-200">
                                                    <div class="">

                                                    </div>
                                                </div>
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
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
