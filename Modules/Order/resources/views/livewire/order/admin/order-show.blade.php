@push('style')
    {{-- <style>
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
    </style> --}}
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
        <x-Order::order.order-info :order="$order" :currency="$currency" />

        <section class="rounded-2xl bg-white shadow-box p-0 overflow-hidden">
            {{-- Todo::complete shippment details --}}
            {{-- <x-Order::order.order-shipped-info :order="$order" :doneSteps="$doneSteps" :totalSteps="$totalSteps" />
            --}}
        </section>


        {{-- {{!- Order Form Card - Main form container -}} --}}
        <section class="rounded-2xl bg-white shadow-box p-6 mb-6">
            <!-- عنوان -->


            <!-- نام مشتری -->

            <!-- جداکننده -->
            <div class="mt-6 border-t border-gray-100"></div>

            {{-- {{!- Price Table: Displays product sizes and pricing -}} --}}
            <!-- Table Container: Handles overflow and scrollability -->
            <livewire:order::products-table :order="$order" />

            <!-- بخش یادداشت‌ها -->
            <div class="mt-8 pt-8 border-t border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-base font-bold text-gray-800">@lang('core::attributes.notes')</h3>
                        <span
                            class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $order->notes->count() }}</span>
                    </div>
                    <button wire:click="addNoteModal"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm text-black border border-black/10 bg-transparent hover:bg-neutral-300 rounded-xl transition duration-200 shadow-sm shadow-[#20BF86]/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        @lang('core::attributes.add_note')
                    </button>
                </div>

                @if($order->notes->isEmpty())
                    <!-- وضعیت خالی یادداشت‌ها -->
                    <div
                        class="flex flex-col items-center justify-center py-8 px-4 border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                        <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-sm text-gray-500 font-semibold mb-1">
                            @lang('order::messages.No_notes_have_been_added_to_this_order_yet')
                        </p>
                        <p class="text-xs text-gray-400">
                            @lang('order::messages.Notes_are_used_for_better_coordination_between_team_members')
                        </p>
                    </div>
                @else
                    <!-- لیست یادداشت‌ها -->
                    <div class="space-y-3">
                        @foreach ($order->notes as $note)
                            <div
                                class="flex items-start gap-3 p-4 border border-gray-100 rounded-2xl bg-gray-50/50 hover:bg-gray-50 transition duration-200">
                                <!-- آواتار نویسنده -->
                                <div
                                    class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0 text-slate-600 font-bold text-sm">
                                    {{ mb_substr($note->creator->name ?? 'ک', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-4 mb-1">
                                        <h4 class="text-sm font-bold text-gray-800 truncate">{{ $note->creator->name }}</h4>
                                        <span class="text-xs text-gray-400 flex-shrink-0">
                                            {{ $note->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $note->value }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="py-4 flex justify-start">
                <div class="mr-2" x-data="{
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
                <button type="button" @click="printInvoice('{{ route('admin.orders.invoice', $order) }}')"
                    class="rounded-lg px-4 py-2 text-sm bg-blue-600 font-semibold text-white hover:bg-blue-700 flex items-center gap-2 transition-all shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] hover:-translate-y-0.5">
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
                @if (
                        !in_array($order->status, [
                            Modules\Order\Enums\OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value,
                            Modules\Order\Enums\OrderStatus::CANCELED->value
                        ])
                    )
                    <a type="button" href="{{ route('admin.orders.tracking.show', $order) }}"
                        class="rounded-lg  px-4 py-2 text-sm  disabled:opacity-40 bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72] mr-2">
                        <span>
                            @lang('order::attributes.order_tracking')
                        </span>
                    </a>
                @endif
                @if ($order->status == Modules\Order\Enums\OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value)
                    @can('orders_approved')

                        <x-Core::button wire:click="approveOrder" target="approveOrder"
                            class="bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72] mr-2">
                            @lang('order::attributes.approve_order')
                        </x-Core::button>
                        <x-Core::button wire:click="openCancelOrderModal" target="openCancelOrderModal"
                            class="bg-red-700 font-semibold  text-white hover:bg-red-800 mr-2">
                            @lang('order::attributes.cancele_order')
                        </x-Core::button>
                    @endcan
                @elseif($order->status == Modules\Order\Enums\OrderStatus::AWAITING_SHIPPED->value)
                    @can('orders_shipped')
                        <x-Core::button wire:click="shipped({{ $order->id }})" target="shipped"
                            class="bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72] ">
                            @lang('order::attributes.shipped')
                        </x-Core::button>
                    @endcan
                @elseif($order->status == Modules\Order\Enums\OrderStatus::SHIPPED->value)
                    @can('orders_shipped')
                        <x-Core::button wire:click="delivered({{ $order->id }})" target="delivered"
                            class="bg-[#20BF86] font-semibold  text-white hover:bg-[#1a9f72] ">
                            @lang('order::attributes.delivered')
                        </x-Core::button>
                    @endcan
                @endif
            </div>
        </section>
    </div>
    <!-- ====== /نمای سفارش نمایندگان ====== -->
    @if($showAddNoteModal)
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4" x-data x-cloak>
            <!-- پس‌زمینه تیره و مات (Premium Glassmorphic Blur) -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300"
                wire:click="$set('showAddNoteModal', false)"></div>

            <!-- محتوای مدال با انیمیشن ورود -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl w-full max-w-lg p-6 relative z-10 transition-all transform duration-300 ease-out"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <!-- هدر مدال -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#20BF86]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        @lang('core::attributes.add_note')
                    </h2>
                    <button wire:click="$set('showAddNoteModal', false)"
                        class="text-gray-400 hover:text-gray-600 transition duration-150 rounded-lg p-1 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- فرم ثبت یادداشت -->
                <form wire:submit.prevent="storeNote">
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">
                                متن یادداشت
                            </label>
                            <textarea wire:model.defer="note"
                                class="w-full border border-gray-200 rounded-2xl p-3 text-sm text-gray-700 placeholder-gray-400 focus:border-[#20BF86] focus:ring focus:ring-[#20BF86]/10 outline-none transition duration-200 resize-none"
                                placeholder="یادداشت خود را در اینجا بنویسید..." rows="5" required></textarea>
                            @error('note')
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
                        <button type="button" wire:click="$set('showAddNoteModal', false)"
                            class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-xl transition duration-200">
                            انصراف
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-white bg-[#20BF86] hover:bg-[#1a9f72] rounded-xl transition duration-200 shadow-sm shadow-[#20BF86]/10 disabled:opacity-40">
                            <span wire:loading.remove wire:target="storeNote">ثبت یادداشت</span>
                            <span wire:loading wire:target="storeNote" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                در حال ثبت...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
