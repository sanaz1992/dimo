<section>
    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">
        <div class="flex flex-col gap-4 md:flex-row justify-between md:items-center">
            <h2 class="font-semibold text-lg md:text-xl">
                {{__('user::attributes.seller') }}
            </h2>
            <div class="flex items-center gap-4">
                @can('sellers_create')
                    <a href="{{route('admin.sellers.create')}}"
                        class="bg-[#3E3E3B] flex items-center gap-2 px-4 py-2 rounded-xl text-white focus:outline-none font-bold">
                        <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5" />
                        <span class="">@lang("user::attributes.create_seller")</span>
                    </a>
                @endcan
            </div>
        </div>
        <div class="overflow-hidden rounded-xl border border-black/10">
            <div class="overflow-x-auto">
                <table class="min-w-[800px] w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            @foreach ($columns as $col)
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $col['label'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $key => $user)
                            <tr class="{{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}">
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    <img alt="{{$user->name}}" class="h-10 w-10 rounded-full object-cover"
                                        src="{{ $user->avatar?->getThumbnailUrl('small') }}">
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    {{$user->name}}
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    {{$user->mobile}}
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    {{$user->addresses->first()->city->name}}
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    {{-- <a href="{{ route('admin.orders.index', ['seller' => $user->unique_code]) }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                                        {{ number_format($user->sellerOrders->count()) }}
                                    </a> --}}
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    {{-- <a
                                        href="{{ route('admin.orders.index', ['seller' => $user->unique_code, 'status' => 'active-statuses']) }}"
                                        class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200">
                                        {{ number_format($user->sellerOrders->whereIn('status', $orderActiveStatuses)->count()) }}
                                    </a> --}}
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    {{ number_format($user->sellerOrders->sum('total_price')) }}
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    {{$user->sellerOrders->count() ? $user->sellerOrders->last()->updated_at_jalali_date : $user->updated_at_jalali_date }}
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    <span
                                        class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{$user->status['color'] }}">
                                        {{$user->status['title'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                    <div class="flex justify-end" x-data="{ showActions: false, menuStyle: '', updatePos(el) { const r = el.getBoundingClientRect(); this.menuStyle = `position:fixed; top:${r.bottom + 8}px; left:${r.left}px;`; } }">
                                        <button
                                            @click="showActions = !showActions; if (showActions) updatePos($el);"
                                            @click.away="showActions = false"
                                            @resize.window="showActions = false"
                                            @scroll.window="showActions = false"
                                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor">
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
                                                    {{-- @can('sellers_show')
                                                    <a href="{{route('admin.sellers.show', $user)}}"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="fas fa-eye ml-2"></i>
                                                        {{__('user::attributes.show_detail') }}
                                                    </a>
                                                    @endcan --}}
                                                    @can('sellers_edit')
                                                        <a href="{{route('admin.sellers.edit', $user)}}"
                                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-edit ml-2"></i>
                                                            {{__('user::attributes.edit') }}
                                                        </a>
                                                    @endcan
                                                    @can('sellers_delete')
                                                        <button type="button"
                                                            onclick="if(confirm('آیا مطمئن هستید؟')) { @this.call('deleteItem', {{ $user->id }}) }"
                                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <i class="fas fa-trash-alt ml-2"></i>
                                                            {{__('user::attributes.delete') }}
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) }}" class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{__('user::messages.without_item') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{$users->links('Core::pagination')}}
    </div>
</section>
