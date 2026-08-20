<header class="header-bar enter-header">
    @php
        $authUser = auth()->user();
        $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class);
    @endphp
    <div class="header-inner relative z-[1] flex w-full flex-wrap items-center gap-2 sm:gap-3">
        <button id="menu-btn" type="button" class="btn-ghost shrink-0 lg:hidden" aria-expanded="false"
            aria-controls="sidebar">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    class="icon-svg shrink-0" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    </path>
                </svg>
            </span>
        </button>
        <div class="min-w-0 flex-1">
            <h1 class="truncate text-lg font-bold text-ink sm:text-xl" data-page-title>
                <a href="{{ route('home') }}">
                    {{$settingHelper->setting('site_title') ? $settingHelper->setting('site_title')?->value : __('core::attributes.company_title')}}
                </a>
            </h1>
            <p class="truncate text-[11px] text-ink-faint sm:text-[12px]" data-page-subtitle>{{$authUser->name}}</p>
        </div>

        <div class="header-btns flex shrink-0 flex-wrap items-center justify-end gap-2">
            {{-- <div class="dropdown-wrap search-dropdown-wrap">
                <button type="button" id="btn-search" class="btn-ghost shrink-0"
                    aria-label="@lang('dashboard::attributes.search')" aria-expanded="false"
                    aria-controls="search-panel">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            class="icon-svg shrink-0" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" fill="currentColor" fill-opacity="0.1"></circle>
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"></circle>
                            <path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                    </span>
                </button>
                <div id="search-panel" class="search-panel hidden" role="search">
                    <label class="search-panel-field" for="search-input">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" class="icon-svg shrink-0" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" fill="currentColor" fill-opacity="0.1"></circle>
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"></circle>
                                <path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                </path>
                            </svg>
                        </span>
                        <input id="search-input" type="search" class="search-panel-input"
                            placeholder="@lang('dashboard::attributes.search')" autocomplete="off" />
                        <button type="button" id="btn-search-clear" class="search-panel-clear hidden"
                            aria-label="@lang('dashboard::attributes.clear')">
                            <span data-icon="close" data-icon-size="xs"></span>
                        </button>
                    </label>
                    <p class="search-panel-hint">@lang('dashboard::messages.search_bottom_description')</p>
                </div>
            </div> --}}
            <a type="button" href="{{ route('instagram.connect') }}" traget="_blank" id="btn-new-tx"
                class="btn-fill btn-new-tx shrink-0">
                <span data-icon="plus" data-icon-size="sm"></span>
                <span class="btn-label">@lang('core::attributes.connect_to_instagram')</span>
            </a>
            {{-- <div class="dropdown-wrap">
                <button type="button" id="btn-notify" class="btn-ghost shrink-0"
                    aria-label="@lang('dashboard::attributes.notifications')" aria-expanded="false"
                    aria-controls="dropdown-notify">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            class="icon-svg shrink-0" aria-hidden="true">
                            <path d="M18 8a6 6 0 1 0-12 0c0 6-2 8-2 8h16s-2-2-2-8" fill="currentColor"
                                fill-opacity="0.12"></path>
                            <path d="M18 8a6 6 0 1 0-12 0c0 6-2 8-2 8h16s-2-2-2-8" stroke="currentColor"
                                stroke-width="1.5" stroke-linejoin="round"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"></path>
                        </svg>
                    </span>
                    <span class="notify-badge" id="notify-count">۳</span>
                </button>
                <div id="dropdown-notify" class="dropdown-notify dropdown-panel dropdown-panel--notify hidden"
                    role="menu">
                    <p class="dropdown-title">@lang('dashboard::attributes.notifications')</p>

                    <button type="button" class="notify-item anim-pop notify-item--unread" data-notify="0"
                        style="animation-delay:0s">
                        <span class="font-semibold text-ink">تراکنش موفق</span>
                        <span class="mt-0.5 block text-[11px] text-ink-faint">سارا احمدی — ۲,۴۵۰,۰۰۰ تومان</span>
                        <span class="mt-1 block text-[10px] text-ink-faint">۲ دقیقه پیش</span>
                    </button>

                    <button type="button" class="notify-item anim-pop notify-item--unread" data-notify="1"
                        style="animation-delay:0.05s">
                        <span class="font-semibold text-ink">کاربر جدید</span>
                        <span class="mt-0.5 block text-[11px] text-ink-faint">رضا اکبری ثبت‌نام کرد</span>
                        <span class="mt-1 block text-[10px] text-ink-faint">۱۵ دقیقه پیش</span>
                    </button>

                    <button type="button" class="notify-item anim-pop notify-item--unread" data-notify="2"
                        style="animation-delay:0.1s">
                        <span class="font-semibold text-ink">هشدار سرور</span>
                        <span class="mt-0.5 block text-[11px] text-ink-faint">بار CPU به ۷۸٪ رسید</span>
                        <span class="mt-1 block text-[10px] text-ink-faint">۱ ساعت پیش</span>
                    </button>

                    <button type="button" class="notify-item anim-pop " data-notify="3"
                        style="animation-delay:0.15000000000000002s">
                        <span class="font-semibold text-ink">گزارش هفتگی</span>
                        <span class="mt-0.5 block text-[11px] text-ink-faint">آماده دانلود است</span>
                        <span class="mt-1 block text-[10px] text-ink-faint">دیروز</span>
                    </button>

                    <button type="button" class="dropdown-action" id="clear-notify">
                        @lang('dashboard::messages.mark_all_as_read')
                    </button>
                </div>
            </div> --}}
            <div class="dropdown-wrap">
                <button type="button" id="btn-profile" class="profile-btn shrink-0" aria-expanded="false"
                    aria-controls="dropdown-profile">
                    <div class="profile-text hidden text-end xs:block sm:block">
                        <p class="max-w-[100px] truncate text-[13px] font-semibold text-ink sm:max-w-none">
                            {{$authUser->name}}
                        </p>
                        <p class="text-[11px] text-ink-faint">{{$authUser->level}}</p>
                    </div>
                    <div class="avatar">{{ substr($authUser->name, 0, 1) }}</div>
                </button>
                <div id="dropdown-profile" class="dropdown-profile dropdown-panel hidden" role="menu">
                    <button type="button" class="dropdown-item" data-goto="settings">
                        @lang('dashboard::attributes.profile')
                    </button>
                    {{-- <button type="button" class="dropdown-item" data-goto="analytics">
                        @lang('dashboard::attributes.reports')
                    </button> --}}
                     <a type="button" class="dropdown-item" id="btn-logout" href="{{ route('logout') }}">
                        @lang('dashboard::attributes.logout')
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>