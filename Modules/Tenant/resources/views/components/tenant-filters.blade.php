


<div class="relative inline-block text-right" dir="rtl">

    <!-- Trigger Button -->
    <button wire:click="toggleModal"
        class="flex items-center gap-2 border px-3 sm:px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm focus:outline-none select-none
        {{ $this->activeFiltersCount() > 0 ? 'border-[#3E3E3B] bg-[#3E3E3B]/10 text-[#3E3E3B]' : 'border-black/10 bg-white text-gray-700' }}">

        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path
                d="M5.52667 14.2C5.20667 14.2 4.89333 14.12 4.6 13.96C4.01333 13.6333 3.66 13.04 3.66 12.3733L3.66 8.84C3.66 8.50667 3.44 8.00667 3.23333 7.75333L0.74 5.11333C0.32 4.69333 0 3.97333 0 3.43333L0 1.9C0 0.833333 0.806667 0 1.83333 0L10.6333 0C11.6467 0 12.4667 0.82 12.4667 1.83333L12.4667 3.3C12.4667 4 12.0467 4.79333 11.6533 5.18667L8.76667 7.74C8.48667 7.97333 8.26667 8.48667 8.26667 8.9L8.26667 11.7667C8.26667 12.36 7.89333 13.0467 7.42667 13.3267L6.50667 13.92C6.20667 14.1067 5.86667 14.2 5.52667 14.2Z"
                fill="currentColor" />
        </svg>
        <span>@lang('core::attributes.filter')</span>

        @if($this->activeFiltersCount() > 0)
            <span
                class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-[#3E3E3B] px-1 text-xs font-black text-white">
                {{ $this->activeFiltersCount() }}
            </span>
        @endif
    </button>

    <!-- Modal / Dropdown -->
    @if($isOpen)
        <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-xs sm:hidden" wire:click="toggleModal"></div>

        <div
            class="bg-white overflow-hidden z-50 fixed bottom-0 inset-x-0 mx-auto w-full rounded-t-[28px] max-h-[85vh] flex flex-col shadow-2xl border-t border-gray-100 sm:fixed sm:bottom-auto sm:top-16 sm:left-0 sm:inset-x-auto sm:w-[520px] sm:rounded-[24px] sm:border">

            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-100 bg-white p-5">
                <div class="flex items-center gap-2">
                    <span class="text-base font-extrabold text-gray-800">
                        @lang('core::attributes.filter')
                    </span>
                </div>
                <button wire:click="clearAll" class="text-xs font-bold text-gray-400 hover:text-red-500 transition-colors">
                    @lang('core::attributes.clear_all')
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto bg-white p-6 space-y-6">

                <div class="grid grid-cols-1 sm:grid-cols-1 gap-x-6 gap-y-5">
                    {{-- Name --}}
                        <x-dashboard::forms.input label="tenant::attributes.name" name="filterData.name"
                            wire:model="filterData.name" />

                        {{-- Timezone --}}
                        <x-dashboard::forms.select label="tenant::attributes.timezone" name="filterData.timezone"
                            wire:model="filterData.timezone" :options="$timezones"
                            placeholder="tenant::messages.select_timezone" />

                        {{-- Local --}}
                        <x-dashboard::forms.select label="tenant::attributes.local" name="filterData.local"
                            wire:model="filterData.local" :options="$locals" placeholder="tenant::messages.select_local" />

                        {{-- Status --}}
                        <x-dashboard::forms.select label="tenant::attributes.status" name="filterData.status"
                            wire:model="filterData.status" :options="$tenantStatuses"
                            placeholder="tenant::messages.select_status" />
                </div>

            </div>

            <!-- Footer -->
            <div class="flex gap-4 border-t border-gray-100 bg-gray-50/50 p-5">
                <x-dashboard::buttons.primary-action id="btn-apply-tenant-filter" tag="button" type="button" size="sm"
                        class="btn-fill flex-1" wire:click="apply" wire:loading.attr="disabled" wire:target="apply">
                        @lang('core::attributes.apply_filter')
                    </x-dashboard::buttons.primary-action>

                    <x-dashboard::buttons.primary-action id="btn-clear-tenant-filter" tag="button" type="button" size="sm"
                        class="flex-1 bg-red-50 text-red-700 ring-red-600/10" wire:click="clearAll"
                        wire:loading.attr="disabled" wire:target="clearAll">
                        @lang('core::attributes.clear_filter')
                    </x-dashboard::buttons.primary-action>
            </div>
        </div>
    @endif
</div>

