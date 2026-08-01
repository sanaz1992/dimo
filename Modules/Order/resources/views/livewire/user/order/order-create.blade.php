@push('style')
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

<div class="container mx-auto rtl">
    {{-- {{!- Agent Order Form Section - Main container for order management -}} --}}
    <div class="container mx-auto rtl space-y-6">
        {{-- {{!- Order Form Card - Main form container -}} --}}
        <section class="rounded-2xl bg-white shadow-box p-6 mb-6">
            <!-- عنوان -->
            <div class="">
                <h1 class="text-[18px] md:text-[20px] font-extrabold tracking-tight">
                    @lang('order::attributes.new_order')
                </h1>
            </div>
            
            <livewire:order::product-selector />

            {{-- {{!- Price Table: Displays product sizes and pricing -}} --}}
            <!-- Table Container: Handles overflow and scrollability -->
            <livewire:order::products-table />

            <div class="relative overflow-x-auto w-full pb-2">
                <div class="flex flex-col gap-2">
                    <label class="shrink-0 text-[13px] font-semibold text-gray-800 w-full">
                        @lang('order::messages.enter_description')
                    </label>
                    <textarea wire:model="form.description" rows="3"
                        class="w-full h-10 rounded-xl border border-gray-200 bg-white px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
            </div>
        </section>
    </div>
    <!-- ====== /نمای سفارش نمایندگان ====== -->
</div>
