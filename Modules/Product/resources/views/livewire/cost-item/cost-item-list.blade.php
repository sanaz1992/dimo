<section>
    <div class="flex flex-col gap-6 bg-white p-6 rounded-2xl shadow-box">
        <div class="flex flex-col gap-4 md:flex-row justify-between md:items-center">
            <h2 class="font-semibold text-[24px]">@lang('product::attributes.cost_items_list')</h2>
            <div class="flex items-center gap-4">

                <button wire:click="openCreateModal()"
                    class="bg-[#3E3E3B] flex items-center gap-2 px-4 py-2 rounded-xl text-white focus:outline-none font-bold">
                    <img src="{{ asset('build/images/icons/header/add.svg') }}" alt="add" class="w-5" />
                    <span class="">@lang("product::attributes.add_cost_item")</span>
                </button>

            </div>
        </div>
        <!-- جدول رنگ ها -->
        <div class="relative">
            <div class="rounded-xl border border-black/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[800px] w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang("core::attributes.row")
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang("product::attributes.title")
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang("product::attributes.base_price") ({{$currency}})
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang("product::attributes.status")
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang("product::attributes.description")
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang("product::attributes.created_at_submit")
                                </th>
                                <th
                                    class="px-4 py-2 sm:px-6 sm:py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    @lang("product::attributes.actions")
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($items as $key => $item)
                                <tr class="hover:bg-gray-50 {{ $key % 2 === 0 ? 'bg-[#F6F6F5]' : '' }}">
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{$loop->index + 1}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{$item->title}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{number_format($item->base_price)}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{$item->is_active ? 'فعال' : 'غیر فعال'}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{$item->description}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">
                                        {{$item->created_at_date}}
                                    </td>
                                    <td class="px-4 py-3 text-sm sm:px-6 sm:py-4">

                                        <button wire:click="openUpdateModal({{ $item->id }})"
                                            wire:key="update-{{ $item->id }}"
                                            class="block text-sm px-4 text-gray-700 hover:bg-gray-100"
                                            style="float: right;">
                                            {{-- @lang("product::attributes.edit") --}}
                                            <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/edit-2.svg') }}"
                                                alt="add" class="w-5" />

                                        </button>
                                        <button wire:click="deleteCostItem({{ $item->id }})"
                                            wire:key="delete-{{ $item->id }}"
                                            class="block text-sm px-4 text-gray-700 hover:bg-gray-100"
                                            style="float: right;">
                                            {{-- @lang("product::attributes.edit") --}}
                                            <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/trash.svg') }}"
                                                alt="add" class="w-5" />

                                        </button>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{__('core::messages.without_item')}}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{$items->links('Core::pagination')}}
        </div>
    </div>

    @if($showCreateModalFlag)
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <!-- پس‌زمینه تیره -->
            <div class="fixed inset-0 bg-gray-800 opacity-50" wire:click="closeCreateModal"></div>

            <!-- محتوای مدال -->
            <div class="bg-white rounded-lg shadow-lg w-1/2 p-6 relative z-10">
                <h2 class="text-lg font-bold mb-4">
                    {{ $selectedItem ? __('product::attributes.edit') . ' ' . $selectedItem->title :
            __('product::attributes.add_cost_item') }}
                </h2>

                <form wire:submit.prevent="createCostItem">
                    <div class="flex flex-col gap-4">
                        <div class="grid gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium">{{__('product::attributes.title')}}</label>
                                <input wire:model="form.title" type="text" class="w-full rounded-lg border-gray-300"
                                    placeholder="{{__('product::messages.enter_title')}}" />
                                @error('form.title')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    @lang('product::attributes.base_price')
                                    ({{$currency}})
                                </label>
                                <div>
                                    <input type="text" class="w-full rounded-lg border-gray-300"
                                        placeholder="{{ __('product::messages.enter_base_price') }}"
                                        oninput="formatNumberInput(this, 'form.base_price')"
                                        value="{{ number_format($form['base_price'] ?? 0) }}" />

                                    <input type="hidden" wire:model.defer="form.base_price" />
                                </div>
                                @error('form.base_price')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium">@lang('product::attributes.description')</label>
                                <input wire:model="form.description" type="text" class="w-full rounded-lg border-gray-300"
                                    placeholder="@lang('product::messages.enter_description')" />
                                @error('form.description')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <div class="text-sm">
                                    <label class="flex items-center" for="is_active">
                                        <input type="checkbox" wire:model="form.is_active" class="ml-2" />
                                        @lang('product::attributes.active')
                                    </label>
                                </div>
                                @error('form.is_active')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <x-Core::button type="submit"
                        class="bg-[#20BF86] mt-2  text-white hover:bg-[#1a9f72] disabled:opacity-40">
                        {{ $selectedItem ? __('product::attributes.update') : __('product::attributes.store') }}
                    </x-Core::button>
                </form>
            </div>
        </div>
    @endif
</section>

@push('scripts')
    @vite('Modules/Core/resources/assets/js/utils.js')
@endpush
