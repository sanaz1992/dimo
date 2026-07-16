<aside id="sidebar" class="sidebar-drawer sidebar-panel lg:self-start" aria-label="@lang('dashboard::attributes.menu')">
    @php
        $authUser = auth()->user();
    @endphp
    <div class="sidebar-head relative z-[1] mb-5 flex items-center justify-between gap-3 sm:mb-6">
        <div class="flex min-w-0 items-center gap-3">
            <div class="logo-box shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <path d="M12 2 4 6.5 12 11l8-4.5L12 2z" fill="currentColor" fill-opacity="0.25"></path>
                    <path d="M4 6.5 12 11v10.5L4 17V6.5z" fill="currentColor" fill-opacity="0.15"></path>
                    <path d="M20 6.5 12 11v10.5l8-4V6.5z" fill="currentColor" fill-opacity="0.12"></path>
                    <path d="M12 2 4 6.5 12 11l8-4.5L12 2zM4 6.5 12 11v10.5L4 17V6.5M20 6.5 12 11v10.5l8-4V6.5"
                        stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"></path>
                </svg>

            </div>
            <div class="min-w-0">
                <p class="truncate text-[15px] font-bold text-ink sm:text-base">
                    @lang('dashboard::attributes.admin_panel')
                </p>
                <p class="text-[11px] text-ink-faint">{{$authUser->name}}</p>
            </div>
        </div>
        <button id="close-btn" type="button" class="btn-ghost shrink-0 lg:hidden"
            aria-label="@lang('dashboard::attributes.close')">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                </svg>
            </span>
        </button>
    </div>

    {{-- <p class="relative z-[1] mb-2 px-2 text-[10px] font-semibold text-ink-faint">منوی اصلی</p> --}}
    <nav class="relative z-[1] flex flex-col gap-1">
        <a href="{{route('admin.dashboard')}}" data-nav="dashboard" class="nav-link nav-link-active">
            <span class="nav-pill" aria-hidden="true"></span>
            <span class="nav-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <rect x="3" y="3" width="8" height="10" rx="2" fill="currentColor" fill-opacity="0.18"></rect>
                    <rect x="13" y="3" width="8" height="6" rx="2" fill="currentColor" fill-opacity="0.12"></rect>
                    <rect x="13" y="13" width="8" height="8" rx="2" fill="currentColor" fill-opacity="0.18"></rect>
                    <rect x="3" y="15" width="8" height="6" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
                    <rect x="13" y="3" width="8" height="6" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
                    <rect x="13" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
                    <rect x="3" y="3" width="8" height="10" rx="2" stroke="currentColor" stroke-width="1.5"></rect>
                </svg>
            </span>
            <span>@lang('dashboard::attributes.dashboard')</span>
        </a>
        @can('users_list')
            <a href="{{route('admin.users.index')}}" data-nav="users" class="nav-link">
                <span class="nav-ico">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        class="icon-svg shrink-0" aria-hidden="true">
                        <circle cx="9" cy="8" r="3.5" fill="currentColor" fill-opacity="0.2"></circle>
                        <path d="M3 20v-1a5 5 0 0 1 5-5h2a5 5 0 0 1 5 5v1" fill="currentColor" fill-opacity="0.15"></path>
                        <circle cx="9" cy="8" r="3.5" stroke="currentColor" stroke-width="1.5"></circle>
                        <path d="M3 20v-1a5 5 0 0 1 5-5h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                        </path>
                        <circle cx="17.5" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"></circle>
                        <path d="M15 20v-.5a3.5 3.5 0 0 1 5 0V20" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round"></path>
                    </svg>
                </span>
                <span>@lang('dashboard::attributes.users')</span>
            </a>
        @endcan
        <a href="#" data-nav="transactions" class="nav-link">
            <span class="nav-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <rect x="2" y="5" width="20" height="14" rx="3" fill="currentColor" fill-opacity="0.12"></rect>
                    <path d="M2 10h20" stroke="currentColor" stroke-width="1.5"></path>
                    <path d="M6 15h4M14 15h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                    <circle cx="7" cy="15" r="0" fill="currentColor"></circle>
                    <rect x="2" y="5" width="20" height="14" rx="3" stroke="currentColor" stroke-width="1.5"></rect>
                    <path d="M6 8h3M15 8h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        opacity="0.7"></path>
                </svg>
            </span>
            <span>@lang('dashboard::attributes.transactions')</span>
        </a>
        <a href="#" data-nav="analytics" class="nav-link">
            <span class="nav-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <path d="M4 20V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M10 20V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M16 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M22 20v-9" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    <rect x="3" y="9" width="4" height="11" rx="1" fill="currentColor" fill-opacity="0.2"></rect>
                    <rect x="9" y="3" width="4" height="17" rx="1" fill="currentColor" fill-opacity="0.25"></rect>
                    <rect x="15" y="14" width="4" height="6" rx="1" fill="currentColor" fill-opacity="0.18"></rect>
                    <rect x="21" y="11" width="4" height="9" rx="1" fill="currentColor" fill-opacity="0.15"
                        transform="translate(-3 0)"></rect>
                </svg>
            </span>
            <span>@lang('dashboard::attributes.analytics')</span>
        </a>
        <a href="#" data-nav="settings" class="nav-link">
            <span class="nav-ico">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <circle cx="12" cy="12" r="3" fill="currentColor" fill-opacity="0.2"></circle>
                    <path
                        d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"></circle>
                </svg>
            </span>
            <span>@lang('dashboard::attributes.setting')</span>
        </a>
    </nav>

    <div class="status-box relative z-[1]">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-soft"
                id="icon-server"></div>
            <div class="min-w-0">
                <p class="text-[11px] text-emerald-700/70">وضعیت سرور</p>
                <p class="text-[13px] font-bold text-emerald-700">آنلاین ۹۹.۹٪</p>
            </div>
        </div>
    </div>
</aside>