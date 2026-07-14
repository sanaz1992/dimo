

<section>
    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">
       <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="font-semibold text-lg md:text-xl">
                    {{__('order::attributes.order_list')}}
                </h2>
                <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                    <!-- Order Type Filter -->
                    <livewire:order::advanced-filters
                        :code="$code"
                        :seller="$seller"
                        :created_at_from="$created_at_from"
                        :created_at_to="$created_at_to"
                        :started_at_from="$started_at_from"
                        :started_at_to="$started_at_to"
                        :finished_at_from="$finished_at_from"
                        :finished_at_to="$finished_at_to"
                        :price_min="$price_min"
                        :price_max="$price_max"
                        :hall="$hall"
                    />

                    <div class="relative">
                        <select
                            wire:model.live="activeTab"
                            wire:change="$dispatch('tabChanged', activeTab)"
                            class="appearance-none bg-white border border-gray-300 rounded-lg px-10 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach ($tabs as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                        </div>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>



                    <!-- منوی مرتب‌سازی -->
                    <livewire:UI::sort-select :options="$sortOptions" :sort-by="$sortBy" />
                    @can('orders_create')
                        <a href="{{route('admin.orders.import')}}"
                            class="border border-[#3E3E3B] flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-[#3E3E3B] focus:outline-none font-bold whitespace-nowrap">
                            <span>@lang("order::attributes.import_orders")</span>
                        </a>
                        <a href="{{route('admin.orders.create')}}"
                            class="bg-[#3E3E3B] flex items-center justify-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-white focus:outline-none font-bold whitespace-nowrap">
                            <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5 flex-shrink-0" />
                            <span class="hidden sm:inline">@lang("order::attributes.new_order")</span>
                            <span class="sm:hidden">@lang("order::attributes.new_order")</span>
                        </a>
                    @endcan
                </div>
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
                                        @lang('order::attributes.type')
                                    </th>
                                    <th
                                        class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @lang('order::attributes.saller_name')
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
                                <?php $authUser = auth()->user(); ?>
                                @forelse ($orders as $key => $order)
                                                                <tr class="hover:bg-gray-50 {{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}">
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                        {{$loop->index + 1}}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                        <a  href="{{ $order->code_link }}" class="text-blue-500 hover:text-blue-700">{{$order->code}}</a>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                        {{$order->created_at_jalali_date}}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                       <span
                                                                            class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{Modules\Order\Enums\OrderType::colors()[$order->type] }}">
                                                                          {{Modules\Order\Enums\OrderType::labels()[$order->type]}}
                                                                          </span>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                        {{$order->seller->name}}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                        {{$order->items->sum('qty')}}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                        {{$order->seller?->addresses?->first()->city?->name?? '-'}}
                                                                    </td>
                                                                    <td>
                                                                        <div class="flex flex-col items-start gap-1">
                                                                            <span
                                                                                class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{Modules\Order\Enums\OrderStatus::colors()[$order->status] }}">
                                                                                {{Modules\Order\Enums\OrderStatus::labels()[$order->status] }}
                                                                            </span>
                                                                            @if ($order->is_pending_pricing)
                                                                                <span
                                                                                    class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap bg-amber-50 text-amber-700 inset-ring-amber-500/10">
                                                                                    @lang('order::attributes.pending_pricing')
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                                                        @if ($order->status == \Modules\Order\Enums\OrderStatus::DRAFT->value)
                                                                            @can('orders_edit')
                                                                                {{-- <a href="{{route('admin.orders.edit', $order)}}"
                                                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                                    <i class="fas fa-edit ml-2"></i>
                                                                                    {{__('order::attributes.edit') }}
                                                                                </a> --}}
                                                                                    <a href="{{route('admin.orders.edit', $order)}}"
                                                                                    class="block text-sm px-2 text-gray-700 hover:bg-gray-100"
                                                                                    style="float: right;">
                                                                                    <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/edit-2.svg') }}"
                                                                                        alt="add" class="w-5" />
                                                                                </a>
                                                                            @endcan
                                                                            @can('orders_delete')
                                                                                <button wire:click="deleteOrder({{ $order->id }})"
                                                                                    wire:key="delete-{{ $order->id }}"
                                                                                    class="block text-sm px-2 text-gray-700 hover:bg-gray-100"
                                                                                    style="float: right;">
                                                                                    <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/trash.svg') }}"
                                                                                        alt="add" class="w-5" />
                                                                                </button>
                                                                            @endcan
                                                                        @else
                                                                            @can('orders_show')
                                                                                <a href="{{route('admin.orders.show', $order)}}"
                                                                                    class="block text-sm text-gray-700 hover:bg-gray-100 p-1"
                                                                                    style="float: right;">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4ab056" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                                                                </a>
                                                                            @endcan
                                                                            @if(
                                                                                    in_array($order->status, [
                                                                                        Modules\Order\Enums\OrderStatus::IN_PRODUCTION->value,
                                                                                        Modules\Order\Enums\OrderStatus::PAUSED->value
                                                                                    ])
                                                                                )
                                                                                @can('order_tracking')
                                                                                    <a href="{{route('admin.orders.tracking.show', $order)}}"
                                                                                        title="@lang('order::attributes.orders_view_production_process') "
                                                                                         class="block text-sm text-gray-700 hover:bg-gray-100 p-1"
                                                                                         style="float: right;">
                                                                                       <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
  <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
</svg>
                                                                                    </a>
                                                                                @endcan
                                                                            @endif

                                                                        @endif
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
                {{$orders->links('Core::pagination')}}
            </div>
        </div>
    </div>


</section>

