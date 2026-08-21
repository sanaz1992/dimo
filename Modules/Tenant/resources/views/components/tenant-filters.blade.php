<div dir="rtl">

    <button type="button" wire:click="openFilterModal" id="filters-popover-trigger-tenant" class="flex items-center gap-2 border px-3 sm:px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm focus:outline-none select-none
    {{ $this->activeFiltersCount() > 0
    ? 'border-[#3E3E3B] bg-[#3E3E3B]/10 text-[#3E3E3B]'
    : 'border-black/10 bg-white text-gray-700' }}">
        <x-Core::icons.filter />

        <span>
            @lang('core::attributes.filter')
        </span>

        @if($this->activeFiltersCount() > 0)
            <span
                class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-[#3E3E3B] px-1 text-xs font-black text-white">
                {{ $this->activeFiltersCount() }}
            </span>
        @endif
    </button>

    @if($showFilterModal)
        <div wire:teleport="body" class="fixed inset-0 z-[99999] flex items-center justify-center p-4"
            wire:click="closeFilterModal">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

            {{-- Modal --}}
            <div class="relative z-10 flex w-full max-w-lg max-h-[90vh] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                role="dialog" aria-modal="true" aria-labelledby="tenant-filter-modal-title" wire:click.stop>

                {{-- Header --}}
                <div class="flex shrink-0 items-center justify-between border-b border-gray-100 bg-white px-5 py-4">

                    <h2 id="tenant-filter-modal-title" class="text-lg font-bold text-ink">
                        @lang('core::attributes.filter')
                    </h2>

                    <button type="button" class="btn-ghost" aria-label="بستن" wire:click="closeFilterModal">
                        <span data-icon="close" data-icon-size="sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                class="icon-svg shrink-0" aria-hidden="true">
                                <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                    </button>

                </div>

                {{-- Body --}}
                <div class="min-h-0 flex-1 overflow-y-auto bg-white p-5">
                    <div class="space-y-4">

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

                {{-- Footer --}}
                <div class="flex shrink-0 gap-3 border-t border-gray-100 bg-white p-5">

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
        </div>
    @endif
</div>
