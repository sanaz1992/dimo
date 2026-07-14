<!-- منوی فیلتر (Frontend-only AlpineJS component) -->
<div x-data="{
    open: false,
    filters: {
        status: '',
        stage: '',
        process_step: '',
        campaign: '',
        agents: [],
        date_from: '',
        date_to: ''
    },
    appliedFilters: {
        status: '',
        stage: '',
        process_step: '',
        campaign: '',
        agents: [],
        date_from: '',
        date_to: ''
    },
    
    // Dropdown open states
    statusDropdownOpen: false,
    stageDropdownOpen: false,
    stepDropdownOpen: false,
    campaignDropdownOpen: false,
    agentsDropdownOpen: false,

    // Options lists
    statusOptions: [
        { id: '', name: 'معاملات در حال پیگیری' },
        { id: 'won', name: 'موفق' },
        { id: 'lost', name: 'ناموفق' },
        { id: 'canceled', name: 'انصراف' }
    ],
    stageOptions: [
        { id: '', name: 'همه مرحله‌ها' },
        { id: 'contact', name: 'تماس اولیه' },
        { id: 'demo', name: 'جلسه حضوری / دمو' },
        { id: 'proposal', name: 'ارسال پروپوزال' },
        { id: 'negotiation', name: 'مذاکره نهایی' }
    ],
    stepOptions: [
        { id: '', name: 'همه گام‌ها' },
        { id: 'step1', name: 'گام ۱: نیازسنجی اولیه' },
        { id: 'step2', name: 'گام ۲: تایید هویت' },
        { id: 'step3', name: 'گام ۳: عقد قرارداد' }
    ],
    campaignOptions: [
        { id: '', name: 'همه کمپین‌ها' },
        { id: 'nowruz', name: 'کمپین نوروز ۱۴۰۵' },
        { id: 'blackfriday', name: 'بلک فرایدی' },
        { id: 'sms', name: 'کمپین پیامکی' }
    ],
    availableAgents: [
        { id: '1', name: 'سارا احمدی' },
        { id: '2', name: 'علی محمدی' },
        { id: '3', name: 'مریم کریمی' },
        { id: '4', name: 'رضا صادقی' }
    ],

    get activeFiltersCount() {
        let count = 0;
        if (this.appliedFilters.status !== '') count++;
        if (this.appliedFilters.stage !== '') count++;
        if (this.appliedFilters.process_step !== '') count++;
        if (this.appliedFilters.campaign !== '') count++;
        if (this.appliedFilters.agents.length > 0) count++;
        if (this.appliedFilters.date_from !== '' || this.appliedFilters.date_to !== '') count++;
        return count;
    },

    init() {
        this.resetToApplied();
        this.$watch('open', value => {
            if (value) {
                this.adjustPosition();
                setTimeout(() => this.adjustPosition(), 30);
            }
        });
    },

    resetToApplied() {
        this.filters.status = this.appliedFilters.status;
        this.filters.stage = this.appliedFilters.stage;
        this.filters.process_step = this.appliedFilters.process_step;
        this.filters.campaign = this.appliedFilters.campaign;
        this.filters.agents = [...this.appliedFilters.agents];
        this.filters.date_from = this.appliedFilters.date_from;
        this.filters.date_to = this.appliedFilters.date_to;
    },

    clearAll() {
        this.filters.status = '';
        this.filters.stage = '';
        this.filters.process_step = '';
        this.filters.campaign = '';
        this.filters.agents = [];
        this.filters.date_from = '';
        this.filters.date_to = '';
        
        this.apply();
    },

    apply() {
        this.appliedFilters.status = this.filters.status;
        this.appliedFilters.stage = this.filters.stage;
        this.appliedFilters.process_step = this.filters.process_step;
        this.appliedFilters.campaign = this.filters.campaign;
        this.appliedFilters.agents = [...this.filters.agents];
        this.appliedFilters.date_from = this.filters.date_from;
        this.appliedFilters.date_to = this.filters.date_to;

        this.$dispatch('notify', {
            type: 'success',
            message: 'فیلترها با موفقیت اعمال شدند'
        });

        this.open = false;
    },

    cancel() {
        this.resetToApplied();
        this.open = false;
        this.closeAllDropdowns();
    },

    closeAllDropdowns() {
        this.statusDropdownOpen = false;
        this.stageDropdownOpen = false;
        this.stepDropdownOpen = false;
        this.campaignDropdownOpen = false;
        this.agentsDropdownOpen = false;
    },

    toggleAgent(id) {
        const index = this.filters.agents.indexOf(id);
        if (index > -1) {
            this.filters.agents.splice(index, 1);
        } else {
            this.filters.agents.push(id);
        }
    },

    getSelectedAgentsText() {
        if (this.filters.agents.length === 0) return 'انتخاب کارشناسان فروش...';
        return this.availableAgents
            .filter(a => this.filters.agents.includes(a.id))
            .map(a => a.name)
            .join('، ');
    },

    adjustPosition() {
        this.$nextTick(() => {
            const dialog = document.getElementById('filters-popover-dialog');
            const trigger = document.getElementById('filters-popover-trigger');
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
            const popoverWidth = 520;
            const margin = 16;
            
            dialog.style.position = 'fixed';
            dialog.style.width = `${popoverWidth}px`;
            
            const spaceBelow = window.innerHeight - triggerRect.bottom - margin;
            const spaceLeft = triggerRect.left - margin;
            const spaceRight = window.innerWidth - triggerRect.right - margin;
            
            // Temporarily set a large max-height to calculate the natural height
            dialog.style.maxHeight = '90vh';
            const dialogHeight = dialog.offsetHeight || 500;
            
            let topVal, leftVal, maxHeightVal;
            
            // Check if bottom placement fits (needs at least dialogHeight + 8px space)
            if (spaceBelow >= dialogHeight + 8) {
                // Strategy 1: Bottom placement (preferred)
                topVal = triggerRect.bottom + 8;
                maxHeightVal = spaceBelow;
                
                // Align horizontally
                if (window.innerWidth - triggerRect.left >= popoverWidth) {
                    leftVal = triggerRect.left;
                } else if (triggerRect.right >= popoverWidth) {
                    leftVal = triggerRect.right - popoverWidth;
                } else {
                    leftVal = triggerRect.left + (triggerRect.width / 2) - (popoverWidth / 2);
                    leftVal = Math.max(margin, Math.min(window.innerWidth - popoverWidth - margin, leftVal));
                }
            } else if (spaceRight >= popoverWidth + 8) {
                // Strategy 2a: Side placement (Right side of trigger)
                leftVal = triggerRect.right + 8;
                
                // Align top with trigger, shift up if it would overflow the viewport
                topVal = triggerRect.top;
                if (topVal + dialogHeight > window.innerHeight - margin) {
                    topVal = window.innerHeight - dialogHeight - margin;
                }
                topVal = Math.max(margin, topVal);
                maxHeightVal = window.innerHeight - topVal - margin;
            } else if (spaceLeft >= popoverWidth + 8) {
                // Strategy 2b: Side placement (Left side of trigger)
                leftVal = triggerRect.left - popoverWidth - 8;
                
                // Align top with trigger, shift up if it would overflow the viewport
                topVal = triggerRect.top;
                if (topVal + dialogHeight > window.innerHeight - margin) {
                    topVal = window.innerHeight - dialogHeight - margin;
                }
                topVal = Math.max(margin, topVal);
                maxHeightVal = window.innerHeight - topVal - margin;
            } else {
                // Strategy 3: Bottom placement Clamped (Viewport too narrow for sides, not enough space below)
                topVal = triggerRect.bottom + 8;
                maxHeightVal = spaceBelow;
                
                // Center or clamp horizontally
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
class="relative inline-block text-right" 
@keydown.escape.window="cancel()" 
@resize.window.debounce.50ms="if(open) adjustPosition()"
@scroll.window="if(open) adjustPosition()"
dir="rtl">

    <!-- Trigger Button -->
    <button
        id="filters-popover-trigger"
        @click="open = !open; if(open) { resetToApplied(); }"
        :class="activeFiltersCount > 0 
            ? 'border-[#3E3E3B] bg-[#3E3E3B]/10 text-[#3E3E3B] hover:bg-[#3E3E3B]/15' 
            : 'border-black/10 bg-white text-gray-700 hover:bg-gray-50'"
        class="flex items-center gap-2 border px-3 sm:px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm focus:outline-none select-none">
       <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
  <g>
    <g>
      <g>
        <path d="M5.52667 14.2C5.20667 14.2 4.89333 14.12 4.6 13.96C4.01333 13.6333 3.66 13.04 3.66 12.3733L3.66 8.84C3.66 8.50667 3.44 8.00667 3.23333 7.75333L0.74 5.11333C0.32 4.69333 0 3.97333 0 3.43333L0 1.9C0 0.833333 0.806667 0 1.83333 0L10.6333 0C11.6467 0 12.4667 0.82 12.4667 1.83333L12.4667 3.3C12.4667 4 12.0467 4.79333 11.6533 5.18667L8.76667 7.74C8.48667 7.97333 8.26667 8.48667 8.26667 8.9L8.26667 11.7667C8.26667 12.36 7.89333 13.0467 7.42667 13.3267L6.50667 13.92C6.20667 14.1067 5.86667 14.2 5.52667 14.2ZM1.83333 1C1.36667 1 1 1.39333 1 1.9L1 3.43333C1 3.68 1.2 4.16 1.45333 4.41333L3.99333 7.08667C4.33333 7.50667 4.66667 8.20667 4.66667 8.83333L4.66667 12.3667C4.66667 12.8 4.96667 13.0133 5.09333 13.08C5.37333 13.2333 5.71333 13.2333 5.97333 13.0733L6.9 12.48C7.08667 12.3667 7.27333 12.0067 7.27333 11.7667L7.27333 8.9C7.27333 8.18667 7.62 7.4 8.12 6.98L10.9733 4.45333C11.2 4.22667 11.4733 3.68667 11.4733 3.29333L11.4733 1.83333C11.4733 1.37333 11.1 1 10.64 1L1.83333 1L1.83333 1Z" fill="#292D32" transform="translate(1.767 0.9)" />
        <path d="M0.502389 6.26905C0.409056 6.26905 0.322389 6.24239 0.235722 6.19572C0.00238895 6.04905 -0.0709441 5.73572 0.0757227 5.50239L3.36239 0.235722C3.50905 0.00238907 3.81572 -0.0709441 4.04905 0.0757225C4.28239 0.222389 4.35572 0.529056 4.20905 0.762387L0.922386 6.02905C0.829053 6.18239 0.669053 6.26905 0.502389 6.26905Z" fill="#292D32" fill-rule="evenodd" transform="translate(3.497 0.897)" />
        <path d="M0 0L16 0L16 16L0 16L0 0Z" />
      </g>
    </g>
  </g>
</svg>
        <span>@lang('core::attributes.filter')</span>
        
        <!-- Active Filter Badge -->
        <template x-if="activeFiltersCount > 0">
            <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-[#3E3E3B] px-1 text-xs font-black text-white" x-text="activeFiltersCount"></span>
        </template>
    </button>

    <!-- Portal / Teleported Overlay and Popover -->
    <template x-teleport="body">
        <div x-show="open" style="display: none;">
            <!-- Overlay Backdrop for Mobile Drawer -->
            <div 
                x-show="open" 
                class="fixed inset-0 z-40 bg-black/40 backdrop-blur-xs sm:hidden" 
                @click="cancel()"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-cloak>
            </div>

            <!-- Dialog / Drawer Container -->
            <div
                id="filters-popover-dialog"
                x-show="open"
                @click.away="if(window.innerWidth >= 640) cancel()"
                class="bg-white overflow-hidden z-50
                       /* Mobile Bottom Drawer styles */
                       fixed bottom-0 inset-x-0 mx-auto w-full rounded-t-[28px] max-h-[85vh] flex flex-col shadow-2xl border-t border-gray-100
                       /* Desktop Popover styles overrides */
                       sm:fixed sm:bottom-auto sm:top-auto sm:inset-x-auto sm:w-[520px] sm:rounded-[24px] sm:border sm:border-gray-100 sm:shadow-2xl"
                x-transition:enter="transition ease-out duration-300 sm:duration-200"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-2 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-2 sm:scale-95"
                x-cloak>

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-100 bg-white p-5">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="text-base font-extrabold text-gray-800">@lang('core::attributes.filter')ها</span>
            </div>
            
            <button 
                @click="clearAll()"
                class="flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-bold text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17" />
                </svg>
                پاک کردن همه
            </button>
        </div>

        <!-- Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto bg-white p-6 space-y-6 max-h-[50vh] sm:max-h-none">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                
                <!-- Status Filter -->
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-bold text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        وضعیت معامله
                    </label>
                    <div class="relative">
                        <!-- Custom Select Trigger -->
                        <div 
                            @click="closeAllDropdowns(); statusDropdownOpen = !statusDropdownOpen"
                            class="w-full border border-black/10 px-3.5 py-2.5 pl-10 pr-3.5 rounded-xl text-sm cursor-pointer flex items-center justify-between select-none bg-white min-h-[44px] hover:border-gray-300 transition-colors">
                            <span class="truncate font-medium text-gray-700" x-text="statusOptions.find(o => o.id === filters.status)?.name || 'معاملات در حال پیگیری'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3 transition-transform duration-200" :class="statusDropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        
                        <!-- Custom Dropdown Menu -->
                        <div 
                            x-show="statusDropdownOpen"
                            @click.away="statusDropdownOpen = false"
                            class="absolute right-0 z-50 mt-1.5 w-full bg-white border border-black/10 rounded-xl shadow-lg p-2 max-h-[200px] overflow-y-auto space-y-1"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            x-cloak>
                            
                            <template x-for="option in statusOptions" :key="option.id">
                                <div 
                                    @click="filters.status = option.id; statusDropdownOpen = false"
                                    :class="filters.status === option.id ? 'bg-gray-100 text-gray-900 font-extrabold' : 'text-gray-700 hover:bg-gray-50'"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer select-none transition-colors">
                                    <span class="text-sm font-medium" x-text="option.name"></span>
                                    <template x-if="filters.status === option.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#3E3E3B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Stage Filter -->
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-bold text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 10a6 6 0 00-12 0v8a6 6 0 0012 0v-8zm-6 4h.01M12 17h.01" />
                        </svg>
                        مرحله معامله
                    </label>
                    <div class="relative">
                        <!-- Custom Select Trigger -->
                        <div 
                            @click="closeAllDropdowns(); stageDropdownOpen = !stageDropdownOpen"
                            class="w-full border border-black/10 px-3.5 py-2.5 pl-10 pr-3.5 rounded-xl text-sm cursor-pointer flex items-center justify-between select-none bg-white min-h-[44px] hover:border-gray-300 transition-colors">
                            <span class="truncate font-medium text-gray-700" x-text="stageOptions.find(o => o.id === filters.stage)?.name || 'همه مرحله‌ها'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3 transition-transform duration-200" :class="stageDropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        
                        <!-- Custom Dropdown Menu -->
                        <div 
                            x-show="stageDropdownOpen"
                            @click.away="stageDropdownOpen = false"
                            class="absolute right-0 z-50 mt-1.5 w-full bg-white border border-black/10 rounded-xl shadow-lg p-2 max-h-[200px] overflow-y-auto space-y-1"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            x-cloak>
                            
                            <template x-for="option in stageOptions" :key="option.id">
                                <div 
                                    @click="filters.stage = option.id; stageDropdownOpen = false"
                                    :class="filters.stage === option.id ? 'bg-gray-100 text-gray-900 font-extrabold' : 'text-gray-700 hover:bg-gray-50'"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer select-none transition-colors">
                                    <span class="text-sm font-medium" x-text="option.name"></span>
                                    <template x-if="filters.stage === option.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#3E3E3B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Process Step Filter -->
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-bold text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        گام در فرآیند
                    </label>
                    <div class="relative">
                        <!-- Custom Select Trigger -->
                        <div 
                            @click="closeAllDropdowns(); stepDropdownOpen = !stepDropdownOpen"
                            class="w-full border border-black/10 px-3.5 py-2.5 pl-10 pr-3.5 rounded-xl text-sm cursor-pointer flex items-center justify-between select-none bg-white min-h-[44px] hover:border-gray-300 transition-colors">
                            <span class="truncate font-medium text-gray-700" x-text="stepOptions.find(o => o.id === filters.process_step)?.name || 'همه گام‌ها'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3 transition-transform duration-200" :class="stepDropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        
                        <!-- Custom Dropdown Menu -->
                        <div 
                            x-show="stepDropdownOpen"
                            @click.away="stepDropdownOpen = false"
                            class="absolute right-0 z-50 mt-1.5 w-full bg-white border border-black/10 rounded-xl shadow-lg p-2 max-h-[200px] overflow-y-auto space-y-1"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            x-cloak>
                            
                            <template x-for="option in stepOptions" :key="option.id">
                                <div 
                                    @click="filters.process_step = option.id; stepDropdownOpen = false"
                                    :class="filters.process_step === option.id ? 'bg-gray-100 text-gray-900 font-extrabold' : 'text-gray-700 hover:bg-gray-50'"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer select-none transition-colors">
                                    <span class="text-sm font-medium" x-text="option.name"></span>
                                    <template x-if="filters.process_step === option.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#3E3E3B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Campaign Filter -->
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-bold text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        کمپین
                    </label>
                    <div class="relative">
                        <!-- Custom Select Trigger -->
                        <div 
                            @click="closeAllDropdowns(); campaignDropdownOpen = !campaignDropdownOpen"
                            class="w-full border border-black/10 px-3.5 py-2.5 pl-10 pr-3.5 rounded-xl text-sm cursor-pointer flex items-center justify-between select-none bg-white min-h-[44px] hover:border-gray-300 transition-colors">
                            <span class="truncate font-medium text-gray-700" x-text="campaignOptions.find(o => o.id === filters.campaign)?.name || 'همه کمپین‌ها'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3 transition-transform duration-200" :class="campaignDropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        
                        <!-- Custom Dropdown Menu -->
                        <div 
                            x-show="campaignDropdownOpen"
                            @click.away="campaignDropdownOpen = false"
                            class="absolute right-0 z-50 mt-1.5 w-full bg-white border border-black/10 rounded-xl shadow-lg p-2 max-h-[200px] overflow-y-auto space-y-1"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            x-cloak>
                            
                            <template x-for="option in campaignOptions" :key="option.id">
                                <div 
                                    @click="filters.campaign = option.id; campaignDropdownOpen = false"
                                    :class="filters.campaign === option.id ? 'bg-gray-100 text-gray-900 font-extrabold' : 'text-gray-700 hover:bg-gray-50'"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer select-none transition-colors">
                                    <span class="text-sm font-medium" x-text="option.name"></span>
                                    <template x-if="filters.campaign === option.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#3E3E3B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Sales Agents Multi-Select -->
                <div class="col-span-1 sm:col-span-2 space-y-2">
                    <label class="flex items-center gap-2 text-xs font-bold text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        کارشناسان فروش
                    </label>
                    
                    <div class="relative">
                        <!-- Selector Input Trigger -->
                        <div 
                            @click="closeAllDropdowns(); agentsDropdownOpen = !agentsDropdownOpen"
                            class="w-full border border-black/10 px-3.5 py-2.5 pl-10 pr-3.5 rounded-xl text-sm text-gray-700 cursor-pointer flex items-center justify-between select-none bg-white min-h-[44px] hover:border-gray-300 transition-colors">
                            <span class="truncate font-medium text-gray-700" :class="filters.agents.length === 0 ? 'text-gray-400' : 'text-gray-700'" x-text="getSelectedAgentsText()"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3 transition-transform duration-200" :class="agentsDropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        
                        <!-- Checkboxes Dropdown -->
                        <div 
                            x-show="agentsDropdownOpen"
                            @click.away="agentsDropdownOpen = false"
                            class="absolute right-0 z-50 mt-1.5 w-full bg-white border border-black/10 rounded-xl shadow-lg p-2 max-h-[200px] overflow-y-auto space-y-1"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            x-cloak>
                            
                            <template x-for="agent in availableAgents" :key="agent.id">
                                <div 
                                    @click="toggleAgent(agent.id)"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-50 cursor-pointer select-none transition-colors">
                                    <input 
                                        type="checkbox"
                                        :checked="filters.agents.includes(agent.id)"
                                        class="h-5 w-5 rounded border-gray-300 text-[#3E3E3B] focus:ring-[#3E3E3B] cursor-pointer"
                                        @click.prevent>
                                    <span class="text-sm text-gray-700 font-medium" x-text="agent.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Date Range Separator -->
            <div class="border-t border-gray-100 pt-5 space-y-3">
                <label class="flex items-center gap-2 text-xs font-bold text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    بازه زمان پیگیری بعدی
                </label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- From Date -->
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="filters.date_from"
                            placeholder="از تاریخ (مثال: ۱۴۰۵/۰۲/۱۵)"
                            class="w-full border border-black/10 px-3.5 py-2.5 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:border-[#3E3E3B] focus:ring-1 focus:ring-[#3E3E3B] transition-colors">
                    </div>
                    <!-- To Date -->
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="filters.date_to"
                            placeholder="تا تاریخ (مثال: ۱۴۰۵/۰۲/۳۰)"
                            class="w-full border border-black/10 px-3.5 py-2.5 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:border-[#3E3E3B] focus:ring-1 focus:ring-[#3E3E3B] transition-colors">
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex gap-4 border-t border-gray-100 bg-gray-50/50 p-5">
            <button
                @click="cancel()"
                class="flex-1 rounded-xl py-3 text-sm font-extrabold text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-all duration-200">
                انصراف
            </button>
            <button
                @click="apply()"
                class="flex-1 rounded-xl bg-[#3E3E3B] py-3 text-sm font-extrabold text-white hover:bg-opacity-95 shadow-sm transition-all duration-200">
                اعمال فیلترها
            </button>
        </div>

    </div>
        </div>
    </template>
</div>
