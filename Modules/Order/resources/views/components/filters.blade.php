<div class="relative inline-block text-right" dir="rtl" x-data="{ open: false }">

    <!-- Trigger Button -->
    <button type="button" @click="open = !open; if(open) { $dispatch('reinit-datepickers') }" id="filters-popover-trigger-order"
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
        <div x-data="{ 
            adjustPosition() {
                this.$nextTick(() => {
                    const dialog = document.getElementById('filters-popover-dialog-order');
                    const trigger = document.getElementById('filters-popover-trigger-order');
                    if (!dialog || !trigger) return;

                    if (window.innerWidth < 640) {
                        dialog.style.position = '';
                        dialog.style.top = '';
                        dialog.style.left = '';
                        dialog.style.right = '';
                        dialog.style.bottom = '';
                        dialog.style.width = '';
                        dialog.style.maxHeight = '';
                        return;
                    }

                    const triggerRect = trigger.getBoundingClientRect();
                    const popoverWidth = 420;
                    const margin = 16;
                    
                    dialog.style.position = 'fixed';
                    dialog.style.width = `${popoverWidth}px`;
                    
                    const spaceBelow = window.innerHeight - triggerRect.bottom - margin;
                    const spaceLeft = triggerRect.left - margin;
                    const spaceRight = window.innerWidth - triggerRect.right - margin;
                    
                    dialog.style.maxHeight = '90vh';
                    const dialogHeight = dialog.offsetHeight || 500;
                    
                    let topVal, leftVal, maxHeightVal;
                    
                    if (spaceBelow >= dialogHeight + 8) {
                        topVal = triggerRect.bottom + 8;
                        maxHeightVal = spaceBelow;
                        
                        if (triggerRect.right >= popoverWidth) {
                            leftVal = triggerRect.right - popoverWidth;
                        } else if (window.innerWidth - triggerRect.left >= popoverWidth) {
                            leftVal = triggerRect.left;
                        } else {
                            leftVal = triggerRect.left + (triggerRect.width / 2) - (popoverWidth / 2);
                            leftVal = Math.max(margin, Math.min(window.innerWidth - popoverWidth - margin, leftVal));
                        }
                    } else if (spaceRight >= popoverWidth + 8) {
                        leftVal = triggerRect.right + 8;
                        topVal = triggerRect.top;
                        if (topVal + dialogHeight > window.innerHeight - margin) {
                            topVal = window.innerHeight - dialogHeight - margin;
                        }
                        topVal = Math.max(margin, topVal);
                        maxHeightVal = window.innerHeight - topVal - margin;
                    } else if (spaceLeft >= popoverWidth + 8) {
                        leftVal = triggerRect.left - popoverWidth - 8;
                        topVal = triggerRect.top;
                        if (topVal + dialogHeight > window.innerHeight - margin) {
                            topVal = window.innerHeight - dialogHeight - margin;
                        }
                        topVal = Math.max(margin, topVal);
                        maxHeightVal = window.innerHeight - topVal - margin;
                    } else {
                        topVal = triggerRect.bottom + 8;
                        maxHeightVal = spaceBelow;
                        leftVal = triggerRect.left + (triggerRect.width / 2) - (popoverWidth / 2);
                        leftVal = Math.max(margin, Math.min(window.innerWidth - popoverWidth - margin, leftVal));
                    }
                    
                    dialog.style.top = `${topVal}px`;
                    dialog.style.bottom = 'auto';
                    dialog.style.left = `${leftVal}px`;
                    dialog.style.right = 'auto';
                    dialog.style.maxHeight = `${maxHeightVal}px`;
                });
            }
        }"
        x-init="adjustPosition(); setTimeout(() => adjustPosition(), 30)"
        @resize.window.debounce.50ms="adjustPosition()"
        @scroll.window="adjustPosition()">
            <template x-teleport="body">
                <div x-show="open" x-cloak>
                    <!-- Mobile Backdrop -->
                    <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-xs sm:hidden" 
                         x-show="open" 
                         x-transition.opacity 
                         @click="open = false"></div>
            
                    <div id="filters-popover-dialog-order"
                         x-show="open"
                         x-transition:enter="transition ease-out duration-300 sm:duration-200"
                         x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-2 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-2 sm:scale-95"
                         @click.away="if(window.innerWidth >= 640 && (!$event.target.closest || (!$event.target.closest('#filters-popover-trigger-order') && !$event.target.closest('.datepicker-container')))) open = false"
                         class="bg-white overflow-hidden z-50 fixed bottom-0 inset-x-0 mx-auto w-full rounded-t-[24px] max-h-[85vh] flex flex-col shadow-2xl sm:fixed sm:bottom-auto sm:top-auto sm:inset-x-auto sm:w-[340px] sm:rounded-[16px] sm:border sm:border-gray-100" dir="rtl">
            
                        <!-- Header -->
                        <div class="flex items-center justify-start border-b border-gray-100 bg-white px-5 py-4">
                            <span class="text-[14px] font-extrabold text-gray-800">
                                @lang('core::attributes.filters')
                            </span>
                        </div>
            
                        <!-- Body -->
                        <div class="flex-1 overflow-y-auto bg-white flex flex-col">
                            
                            <!-- Code -->
                            <div class="flex flex-col gap-2 p-4 border-b border-gray-50">
                                <div class="flex justify-between items-center">
                                    <label class="text-[13px] font-bold text-gray-800">@lang('order::attributes.code')</label>
                                    <button type="button" wire:click="$set('code', '')" class="text-[12px] font-bold text-[#3478F6] hover:text-blue-700 transition-colors">ریست کردن</button>
                                </div>
                                <input type="text" wire:model="code" value="{{ $code }}"
                                    placeholder="@lang('order::attributes.code')"
                                    class="w-full h-[44px] border border-gray-200 px-4 py-2 rounded-xl text-[13px] focus:border-[#3478F6] focus:ring-1 focus:ring-[#3478F6] outline-none transition-colors placeholder:text-gray-400">
                            </div>
            
                            <!-- Seller -->
                            <div class="flex flex-col gap-2 p-4 border-b border-gray-50">
                                <div class="flex justify-between items-center">
                                    <label class="text-[13px] font-bold text-gray-800">@lang('order::attributes.saller_name')</label>
                                    <button type="button" wire:click="$set('seller', '')" class="text-[12px] font-bold text-[#3478F6] hover:text-blue-700 transition-colors">ریست کردن</button>
                                </div>
                                <input type="text" wire:model="seller" value="{{$seller}}"
                                    placeholder="@lang('order::messages.placeholder.search_by_seller')"
                                    class="w-full h-[44px] border border-gray-200 px-4 py-2 rounded-xl text-[13px] focus:border-[#3478F6] focus:ring-1 focus:ring-[#3478F6] outline-none transition-colors placeholder:text-gray-400">
                            </div>

            

                            <!-- Date Range: Started At -->
                            <div class="flex flex-col gap-2 p-4 border-b border-gray-50">
                                <div class="flex justify-between items-center">
                                    <label class="text-[13px] font-bold text-gray-800">تاریخ شروع را وارد کنید</label>
                                    <button type="button" wire:click="$set('started_at_from', ''); $set('started_at_to', '')"
                                     onclick="document.querySelectorAll('.persianDate').forEach(el => el.value = '')" 
                                     class="text-[12px] font-bold text-[#3478F6] hover:text-blue-700 transition-colors">ریست کردن</button>
                                </div>
                                
                                <div class="flex items-center border border-gray-200 rounded-xl bg-white overflow-hidden h-[44px] focus-within:border-[#3478F6] focus-within:ring-1 focus-within:ring-[#3478F6] transition-all">
                                    <input type="text" data-model="started_at_from" value="{{ $started_at_from }}"
                                        placeholder="تاریخ شروع از"
                                        class="persianDate w-full h-full border-none px-3 text-[13px] outline-none bg-transparent placeholder:text-gray-400 focus:ring-0 text-right">
                                        
                                    <div class="w-px h-5 bg-gray-200 shrink-0"></div>
                                    
                                    <input type="text" data-model="started_at_to" value="{{ $started_at_to }}"
                                        placeholder="تاریخ شروع تا"
                                        class="persianDate w-full h-full border-none px-3 text-[13px] outline-none bg-transparent placeholder:text-gray-400 focus:ring-0 text-right">
                                    
                                    <div class="px-3 text-gray-400 flex items-center h-full border-r border-gray-100 bg-gray-50/50">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Range: Finished At -->
                            <div class="flex flex-col gap-2 p-4 border-b border-gray-50">
                                <div class="flex justify-between items-center">
                                    <label class="text-[13px] font-bold text-gray-800">تاریخ پایان را وارد کنید</label>
                                    <button type="button" wire:click="$set('finished_at_from', ''); $set('finished_at_to', '')" onclick="document.querySelectorAll('.persianDate').forEach(el => el.value = '')" class="text-[12px] font-bold text-[#3478F6] hover:text-blue-700 transition-colors">ریست کردن</button>
                                </div>
                                
                                <div class="flex items-center border border-gray-200 rounded-xl bg-white overflow-hidden h-[44px] focus-within:border-[#3478F6] focus-within:ring-1 focus-within:ring-[#3478F6] transition-all">
                                    <input type="text" data-model="finished_at_from" value="{{ $finished_at_from }}"
                                        placeholder="تاریخ پایان از"
                                        class="persianDate w-full h-full border-none px-3 text-[13px] outline-none bg-transparent placeholder:text-gray-400 focus:ring-0 text-right">
                                        
                                    <div class="w-px h-5 bg-gray-200 shrink-0"></div>
                                    
                                    <input type="text" data-model="finished_at_to" value="{{ $finished_at_to }}"
                                        placeholder="تاریخ پایان تا"
                                        class="persianDate w-full h-full border-none px-3 text-[13px] outline-none bg-transparent placeholder:text-gray-400 focus:ring-0 text-right">
                                    
                                    <div class="px-3 text-gray-400 flex items-center h-full border-r border-gray-100 bg-gray-50/50">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="flex flex-col gap-3 p-4 border-b border-gray-50" x-data="{
                                min: @entangle('price_min'),
                                max: @entangle('price_max'),
                                minLimit: 0,
                                maxLimit: 100000000,
                                minVal: 0,
                                maxVal: 100000000,
                                init() {
                                    this.minVal = this.min || this.minLimit;
                                    this.maxVal = this.max || this.maxLimit;
                                    this.$watch('minVal', val => { this.min = val; });
                                    this.$watch('maxVal', val => { this.max = val; });
                                },
                                formatPrice(val) {
                                    return new Intl.NumberFormat('fa-IR').format(val) + ' تومان';
                                }
                            }">
                                <style>
                                    .range-slider-input::-webkit-slider-thumb {
                                        pointer-events: auto;
                                        width: 20px;
                                        height: 20px;
                                        border-radius: 50%;
                                        background: #ffffff;
                                        border: 2px solid #3478F6;
                                        cursor: pointer;
                                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                        -webkit-appearance: none;
                                    }
                                    .range-slider-input::-moz-range-thumb {
                                        pointer-events: auto;
                                        width: 20px;
                                        height: 20px;
                                        border-radius: 50%;
                                        background: #ffffff;
                                        border: 2px solid #3478F6;
                                        cursor: pointer;
                                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                                    }
                                </style>
                                <div class="flex justify-between items-center">
                                    <label class="text-[13px] font-bold text-gray-800">محدوده قیمت (تومان)</label>
                                    <button type="button" @click="minVal = minLimit; maxVal = maxLimit;" class="text-[12px] font-bold text-[#3478F6] hover:text-blue-700 transition-colors">ریست کردن</button>
                                </div>
                                
                                <div class="relative w-full pt-8 pb-2 px-8">
                                    <div class="relative w-full h-2 bg-gray-100 rounded-lg">
                                        <!-- Highlighted Track -->
                                        <div class="absolute h-2 bg-[#3478F6] rounded-lg"
                                             :style="'right: ' + ((minVal - minLimit) / (maxLimit - minLimit) * 100) + '%; left: ' + (100 - ((maxVal - minLimit) / (maxLimit - minLimit) * 100)) + '%'">
                                        </div>
                                        
                                        <!-- Thumbs / Inputs -->
                                        <input type="range" :min="minLimit" :max="maxLimit" step="10000" x-model.number="minVal"
                                               class="range-slider-input absolute pointer-events-none appearance-none z-20 h-2 w-full opacity-0 cursor-pointer"
                                               @input="if(minVal > maxVal) minVal = maxVal">
                                        <input type="range" :min="minLimit" :max="maxLimit" step="10000" x-model.number="maxVal"
                                               class="range-slider-input absolute pointer-events-none appearance-none z-20 h-2 w-full opacity-0 cursor-pointer"
                                               @input="if(maxVal < minVal) maxVal = minVal">
                                               
                                        <!-- Badges -->
                                        <div class="absolute -top-9 transform translate-x-1/2 bg-[#3478F6] text-white text-[11px] font-bold px-2.5 py-1 rounded-lg shadow-sm whitespace-nowrap z-30 pointer-events-none"
                                             :style="'right: ' + ((minVal - minLimit) / (maxLimit - minLimit) * 100) + '%'">
                                             <span x-text="formatPrice(minVal)"></span>
                                        </div>
                                        <div class="absolute -top-9 transform translate-x-1/2 bg-[#3478F6] text-white text-[11px] font-bold px-2.5 py-1 rounded-lg shadow-sm whitespace-nowrap z-30 pointer-events-none"
                                             :style="'right: ' + ((maxVal - minLimit) / (maxLimit - minLimit) * 100) + '%'">
                                             <span x-text="formatPrice(maxVal)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hall Select -->
                            <div class="flex flex-col gap-2 p-4">
                                <div class="flex justify-between items-center">
                                    <label class="text-[13px] font-bold text-gray-800">سالن خود را انتخاب کنید</label>
                                    <button type="button" wire:click="$set('hall', '')" class="text-[12px] font-bold text-[#3478F6] hover:text-blue-700 transition-colors">ریست کردن</button>
                                </div>
                                <div class="relative mt-1">
                                    <x-custom-select
                                        compact
                                        class="w-full h-[44px]"
                                        :selected="collect($halls)->where('slug', $hall)->first()?->title ?? ''"
                                        placeholder="انتخاب سالن"
                                    >
                                        @foreach ($halls as $h)
                                            <button type="button" x-on:click="choose('{{ $h->slug }}', '{{ $h->title }}')" wire:click="$set('hall', '{{ $h->slug }}')"
                                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-right text-xs font-bold text-gray-800 transition hover:bg-gray-50 hover:text-gray-900">
                                                <span>{{ $h->title }}</span>
                                            </button>
                                        @endforeach
                                    </x-custom-select>
                                </div>
                            </div>
            
                        </div>
            
                        <!-- Footer -->
                        <div class="flex gap-3 bg-white p-5 pt-3 border-t border-gray-50">
                            <button type="button" wire:click="clearAll" @click="open = false" onclick="document.querySelectorAll('.persianDate').forEach(el => el.value = '')" class="flex-1 rounded-xl bg-[#EBEBEB] py-3 text-[13px] font-bold text-gray-600 hover:bg-gray-300 transition-colors">
                                ریست کردن فیلتر
                            </button>
                            <button type="button" wire:click="apply" @click="open = false" class="flex-1 rounded-xl bg-[#3478F6] py-3 text-[13px] font-bold text-white hover:bg-blue-600 transition-colors shadow-sm shadow-blue-500/20">
                                تایید فیلتر
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
</div>
