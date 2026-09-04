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
                    @lang('dashboard::attributes.user_panel')
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
        <a href="{{route('user.dashboard')}}" data-nav="dashboard"
            class="nav-link {{request()->routeIs('user.dashboard') ? 'nav-link-active' : ''}} ">
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

        <a href="{{route('user.tenants.index')}}" data-nav="tenants"
            class="nav-link {{request()->routeIs('user.tenants.*') ? 'nav-link-active' : ''}}">
            <span class="nav-ico">
                <img src="{{ asset('icons\sidebar\manager.svg') }}" alt="tenants" />
            </span>
            <span>@lang('dashboard::attributes.my_tenants')</span>
        </a>

        <a href="{{route('user.instagram_accounts.index')}}" data-nav="instagram_accounts"
            class="nav-link {{request()->routeIs('user.instagram_accounts.*') ? 'nav-link-active' : ''}}">
            <span class="nav-ico">
                <img src="{{ asset('icons\sidebar\instagram-white.svg') }}" alt="instagram_accounts" />
            </span>
            <span>@lang('dashboard::attributes.my_instagram_accounts')</span>
        </a>

        <a href="{{route('user.automation_rules.index')}}" data-nav="automation_rules"
            class="nav-link {{request()->routeIs('user.automation_rules.*') ? 'nav-link-active' : ''}}">
            <span class="nav-ico">
                <img src="{{request()->routeIs('user.automation_rules.*') ? asset('icons\sidebar\magic-stick-white.svg') : asset('icons\sidebar\magic-stick.svg') }}"
                    alt="automation_rules" />
            </span>
            <span>@lang('dashboard::attributes.automation_rules')</span>
        </a>

    </nav>


</aside>
