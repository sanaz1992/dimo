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
            <p class="text-sm font-semibold">{{auth()->user()->name}}</p>
            <p class="text-xs text-black/60">{{auth()->user()->mobile}}</p>
            <p class="text-xs text-black/60">@lang('core::attributes.admin')</p>
        </div>
    </div>

    <!-- Scrollable Content Container -->
    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        <!-- Navigation Links -->
        <nav class="flex flex-col gap-2 flex-1 sidebar-scrollbar overflow-y-auto" x-data="{ openMenus: {} }">
            <!-- Regular Link Item (Non-collapsible) -->
            <a href="{{route('admin.dashboard')}}"
                class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-sm sidebar-close-link
                {{ request()->routeIs('admin.dashboard') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100' }}">
                <img src="{{ asset('build/images/icons/sidebar/home.svg') }}"
                    class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'brightness-0 invert' : '' }}"
                    alt="@lang('core::attributes.dashboard')" />
                @lang('core::attributes.dashboard')
            </a>

            @canany(['sellers_list', 'sellers_create'])
                <div x-data="{ isOpen: {{ request()->routeIs('admin.sellers.*') ? 'true' : 'false' }} }">
                    <button @click="isOpen = !isOpen"
                        class="w-full flex items-center justify-between gap-2 px-3 py-3 rounded-xl transition font-medium text-sm text-black hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('build/images/icons/sidebar/sellers.svg') }}" class="w-5 h-5"
                                alt="@lang('user::attributes.sellers')" />
                            @lang('user::attributes.sellers')
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Submenu Items -->
                    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" style="display: none">
                        <div class="flex flex-col gap-1 pr-4 mt-1">
                            @can('sellers_create')
                                <a href="{{ route('admin.sellers.create') }}"
                                    class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-sm sidebar-close-link
                                                                                                                                                                                                                                                                                                                 {{ request()->routeIs('admin.sellers.create') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.sellers.create') ? 'bg-white' : 'bg-black/30' }}"></span>
                                    @lang('user::attributes.new_seller')
                                </a>
                            @endcan
                            @can('sellers_list')
                                <a href="{{ route('admin.sellers.index') }}"
                                    class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-sm sidebar-close-link
                                                                                                                                                                                                                                                                                                                 {{ request()->routeIs('admin.sellers.index') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.sellers.index') ? 'bg-white' : 'bg-black/30' }}"></span>
                                    @lang('user::attributes.sellers_list')
                                </a>
                            @endcan

                        </div>
                    </div>
                </div>
            @endcanany

            <!-- Collapsible Menu Item: Orders -->
            @canany(['settings_edit', 'admins_list', 'roles_list'])
                <div
                    x-data="{ isOpen: {{ request()->routeIs(['admin.settings.*', 'admin.admins.*', 'admin.roles.*']) ? 'true' : 'false' }} }">
                    <button @click="isOpen = !isOpen"
                        class="w-full flex items-center justify-between gap-2 px-3 py-3 rounded-xl transition font-medium text-sm text-black hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('build/images/icons/sidebar/permissions.svg') }}" class="w-5 h-5"
                                alt=" @lang('core::attributes.settings')" />
                            @lang('core::attributes.settings')
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Submenu Items -->
                    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" style="display: none">
                        <div class="flex flex-col gap-1 pr-4 mt-1">
                            @can('settings_edit')
                                <a href=" {{route('admin.settings.edit')}}"
                                    class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-sm sidebar-close-link
                                                                                                                                                                                                                                                                                                                                                                                                                                                                         {{ request()->routeIs('admin.settings.edit') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.settings.edit') ? 'bg-white' : 'bg-black/30' }}"></span>
                                    @lang('core::attributes.panel_settings')
                                </a>
                            @endcan
                            @can('admins_list')
                                <a href="{{route('admin.admins.index')}}"
                                    class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-sm sidebar-close-link
                                                                                                                                                                                                                                                                                                                                                                                                                                                             {{ request()->routeIs('admin.admins.index') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.admins.index') ? 'bg-white' : 'bg-black/30' }}"></span>
                                    @lang('user::attributes.admins')
                                </a>
                            @endcan
                            @can('roles_list')
                                <a href="{{route('admin.roles.index')}}"
                                    class="flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-sm sidebar-close-link
                                                                                                                                                                                                                                                                                                                                                                                                                                                         {{ request()->routeIs('admin.roles.index') ? 'bg-[#3E3E3B] text-white' : 'text-black hover:bg-gray-100' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.roles.index') ? 'bg-white' : 'bg-black/30' }}"></span>
                                    @lang('user::attributes.permissions')
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endcanany
        </nav>
    </div>

    <!-- Logout Button -->
    <div class="pt-2 border-t border-black/10 bg-white flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class=" flex items-center gap-2 px-3 py-3 rounded-xl transition font-medium text-black
                hover:bg-gray-100 sidebar-close-link">
                <img src="{{ asset('build/images/icons/sidebar/logout.svg') }}" class="w-5 h-5" alt="خروج" />
                خروج
            </button>
        </form>
    </div>
</aside>
