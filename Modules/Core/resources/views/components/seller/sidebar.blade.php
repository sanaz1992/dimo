<style>
    .sidebar-scrollbar::-webkit-scrollbar {
        width: 2px;
    }

    .sidebar-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scrollbar::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 1px;
    }

    .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #9ca3af;
    }

    /* For Firefox */
    .sidebar-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
    }
</style>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay"
    class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300 ease-linear">
</div>

<!-- Sidebar -->
<aside id="sidebar"
    class="fixed lg:sticky right-0 top-0 lg:top-8 bg-white w-64 shadow-box lg:rounded-2xl p-4 z-50 transition-all duration-300 ease-in-out lg:block flex flex-col h-screen lg:min-h-[calc(100vh-12rem)] self-start translate-x-full lg:translate-x-0 overflow-hidden">
    <!-- Mobile Close Button -->
    <div class="flex justify-end mb-4 lg:hidden sticky top-0 z-10 bg-white">
        <button id="sidebar-close-btn" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- User Profile Section -->
    <div class="flex items-center gap-2 border-b border-black/10 pb-6 mb-4 flex-shrink-0">
        <div class="w-12 h-12 rounded-full bg-gray-200">
            <img alt="{{auth()->user()->name}}" class="rounded-full"
                src="{{ auth()->user()->avatar?->getThumbnailUrl('small') }}">
        </div>
        <div>
            <a href="{{ route('seller.profile') }}">
                <p class="text-sm font-semibold">{{auth()->user()->name}}</p>

                <p class="text-xs text-black/60">{{auth()->user()->mobile}}</p>
                <p class="text-xs text-black/60">@lang('core::attributes.seller')</p>
            </a>
        </div>
    </div>

    <!-- Scrollable Content Container -->
    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        <!-- Navigation Links -->
        <nav class="flex flex-col gap-2 overflow-y-auto flex-1 sidebar-scrollbar" x-data="{ openMenus: {} }">

            <!-- Regular Link Item (Non-collapsible) -->
            <a href="{{route('seller.dashboard')}}"
                class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium sidebar-close-link {{ request()->routeIs('seller.dashboard') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100' }}">
                <img src="{{ asset('build/images/icons/sidebar/dashboard.svg') }}" class="w-5 h-5 {{ request()->routeIs('seller.dashboard') ? 'brightness-0 invert' : '' }}"
                    alt="@lang('core::attributes.dashboard')" />
                @lang('core::attributes.dashboard')
            </a>

            <!-- Regular Link Item (Non-collapsible) -->
            <a href="{{ route('seller.orders.index') }}"
                class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium sidebar-close-link
                                                                        {{request()->routeIs('seller.orders.index') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100'}}">
                <img src="{{ asset('build/images/icons/sidebar/orders.svg') }}" class="w-5 h-5 {{ request()->routeIs('seller.orders.index') ? 'brightness-0 invert' : '' }}"
                    alt="@lang('core::attributes.orders')" />
                @lang('core::attributes.orders')
            </a>

            <a href="{{ route('seller.shipments.index') }}"
                class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium sidebar-close-link
                                                                        {{request()->routeIs('seller.shipments.index') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100'}}">
                <img src="{{ asset('build/images/icons/dashboard/vuesax/outline/truck-fast.svg') }}"
                            class="w-5 h-5" alt="@lang('shipment::attributes.loading')" />
                @lang('shipment::attributes.loading')
            </a>
        </nav>
    </div>

    <!-- Logout Button -->
    <div class="pt-2 border-t border-black/10 bg-white flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-black hover:bg-gray-100"
                @click="sidebarOpen = false">
                <img src="{{ asset('build/images/icons/sidebar/logout.svg') }}" class="w-5 h-5" alt="خروج" />
                خروج
            </button>
        </form>
    </div>
</aside>
