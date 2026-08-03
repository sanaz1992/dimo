<div>
    <section class=" gap-4 anim-stagger">
        <x-Order::order.order-info :order="$order" :currency="$currency" />
    </section>

    {{-- <section class=" gap-4 anim-stagger">
        <x-Order::order.order-shipped-info :order="$order" :doneSteps="$doneSteps" :totalSteps="$totalSteps" />
    </section> --}}

    <section class=" gap-4 anim-stagger">
        <livewire:order::products-table :order="$order" />
    </section>

    <section class=" gap-4 anim-stagger mt-3">
        @if ($order->status == Modules\Order\Enums\OrderStatus::PAID)
            <x-dashboard::buttons.primary-action id="btn-approve-order" tag="button"
                class="inline-flex items-center justify-center px-4 py-2 text-sm rounded-xl btn-fill btn-new-tx shrink-0 !bg-gradient-to-r !from-emerald-600 !to-teal-500 hover:!from-emerald-700 hover:!to-teal-600 text-white"
                wire:click="approveOrder" target="approveOrder">
                @lang('order::attributes.approve_order')
            </x-dashboard::buttons.primary-action>

            <x-dashboard::buttons.primary-action id="btn-approve-order" tag="button"
                class="inline-flex items-center justify-center px-4 py-2 text-sm rounded-xl btn-fill btn-new-tx shrink-0 !bg-gradient-to-r !from-red-600 !to-rose-500 hover:!from-red-700 hover:!to-rose-600 text-white"
                wire:click="openCancelOrderModal" target="openCancelOrderModal">
                @lang('order::attributes.cancel_order')
            </x-dashboard::buttons.primary-action>
        @elseif($order->status==Modules\Order\Enums\OrderStatus::PROCESSING)
            <x-dashboard::buttons.primary-action id="btn-done-proccessing" tag="button"
                class="inline-flex items-center justify-center px-4 py-2 text-sm rounded-xl btn-fill btn-new-tx shrink-0 !bg-gradient-to-r !from-blue-600 !to-indigo-600 hover:!from-blue-700 hover:!to-indigo-700 text-white"
                wire:click="doneProccessing" target="doneProccessing">
                @lang('order::attributes.awaiting_shipped')
            </x-dashboard::buttons.primary-action>
        @elseif($order->status==Modules\Order\Enums\OrderStatus::AWAITING_SHIPPED)
            <x-dashboard::buttons.primary-action id="btn-shipped" tag="button"
                class="inline-flex items-center justify-center px-4 py-2 text-sm rounded-xl btn-fill btn-new-tx shrink-0 !bg-gradient-to-r !from-blue-600 !to-indigo-600 hover:!from-blue-700 hover:!to-indigo-700 text-white"
                wire:click="shipped" target="shipped">
                @lang('order::attributes.shipped')
            </x-dashboard::buttons.primary-action>
        @elseif($order->status==Modules\Order\Enums\OrderStatus::SHIPPED)
            <x-dashboard::buttons.primary-action id="btn-delivered" tag="button"
                class="inline-flex items-center justify-center px-4 py-2 text-sm rounded-xl btn-fill btn-new-tx shrink-0 !bg-gradient-to-r !from-blue-600 !to-indigo-600 hover:!from-blue-700 hover:!to-indigo-700 text-white"
                wire:click="delivered" target="delivered">
                @lang('order::attributes.delivered')
            </x-dashboard::buttons.primary-action>
        @endif
    </section>

    @if($showCancelOrderModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4" x-data x-cloak>
            <!-- پس‌زمینه تیره و مات (Premium Glassmorphic Blur) -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300"
                wire:click="$set('showCancelOrderModal', false)"></div>

            <!-- محتوای مدال با انیمیشن ورود -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl w-full max-w-lg p-6 relative z-10 transition-all transform duration-300 ease-out"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <!-- هدر مدال -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.4">
                                <path d="M13.3906 17.3604L10.6406 14.6104" stroke="currentColor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M13.3594 14.6396L10.6094 17.3896" stroke="currentColor" stroke-width="1.5"
                                    stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            </g>
                            <path opacity="0.4" d="M8.80994 2L5.18994 5.63" stroke="currentColor" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path opacity="0.4" d="M15.1899 2L18.8099 5.63" stroke="currentColor" stroke-width="1.5"
                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M2 7.8501C2 6.0001 2.99 5.8501 4.22 5.8501H19.78C21.01 5.8501 22 6.0001 22 7.8501C22 10.0001 21.01 9.8501 19.78 9.8501H4.22C2.99 9.8501 2 10.0001 2 7.8501Z"
                                stroke="currentColor" stroke-width="1.5" />
                            <path d="M3.5 10L4.91 18.64C5.23 20.58 6 22 8.86 22H14.89C18 22 18.46 20.64 18.82 18.76L20.5 10"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        @lang('order::attributes.cancele_order')
                    </h2>
                    <button wire:click="$set('showCancelOrderModal', false)"
                        class="text-gray-400 hover:text-gray-600 transition duration-150 rounded-lg p-1 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- فرم ثبت یادداشت -->
                <form wire:submit.prevent="cancelOrder">
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">
                                @lang('order::attributes.description')
                            </label>
                            <textarea wire:model.defer="cancel_description" required
                                class="w-full border border-gray-200 rounded-2xl p-3 text-sm text-gray-700 placeholder-gray-400 focus:border-[#20BF86] focus:ring focus:ring-[#20BF86]/10 outline-none transition duration-200 resize-none"
                                placeholder="@lang('order::messages.enter_description')" rows="5" required></textarea>
                            @error('cancel_description')
                                <div class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- دکمه‌های کنترل -->
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="$set('showCancelOrderModal', false)"
                            class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition duration-200">
                            @lang('core::attributes.cancel')
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="cancelOrder"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white bg-[#20BF86] hover:bg-[#1a9f72] rounded-xl transition duration-200 shadow-sm shadow-[#20BF86]/10 disabled:opacity-40">
                            <span wire:loading.remove wire:target="cancelOrder">@lang('core::attributes.store') </span>
                            <span wire:loading wire:target="cancelOrder" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                @lang('core::messages.submitting')
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
