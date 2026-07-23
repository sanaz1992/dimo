<header class="topbar">
    @php
        $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class);
    @endphp
    <div class="container topbar-inner">
        <nav class="nav">
            <div class="brand">
                <div class="logo" aria-hidden="true"></div>
                <div class="brand-text">
                    <strong>{{$settingHelper->setting('site_title') ? $settingHelper->setting('site_title')?->value : ''}}</strong>
                    <span>{{$settingHelper->setting('sub_title') ? $settingHelper->setting('sub_title')?->value : ''}}</span>
                </div>
            </div>

            <div class="menu">
                <a class="{{ request()->routeIs('home') ? 'active' : '' }}"
                    href="{{ route('home') }}">@lang('shop::attributes.home_page')</a>
                <a class="{{ request()->routeIs('products.index') ? 'active' : '' }}"
                    href="{{ route('products.index') }}">@lang('product::attributes.products')</a>
                <a class="{{ request()->routeIs('about.index') ? 'active' : '' }}"
                    href="{{ route('about.index') }}">@lang('pages::attributes.about')</a>
                <a class="{{ request()->routeIs('blogs.index') ? 'active' : '' }}"
                    href="{{ route('blogs.index') }}">@lang('blog::attributes.blog')</a>
                <a class="{{ request()->routeIs('contactus.index') ? 'active' : '' }}"
                    href="{{route('contactus.index')}}">@lang('pages::attributes.contactus')</a>
            </div>

            <div class="actions">
                <button class="icon-btn" aria-label="@lang('core::attributes.search')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.5-3.5"></path>
                    </svg>
                </button>
                @livewire('cart::guest.cart-icon')
                @auth
                    <div class="user-menu-wrapper" style="position: relative; display: inline-block;">
                        <a href="{{ route('admin.dashboard') }}" class="icon-btn user-profile-btn" aria-label="داشبورد کاربری"
                          >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            {{-- <span class="user-name" style="font-weight: 500;">
                                {{ substr(auth()->user()->name, 0, 1) ?: auth()->user()->mobile ?: 'حساب کاربری' }}
                            </span> --}}
                        </a>
                    </div>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="icon-btn" aria-label="ورود / ثبت نام">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </a>
                @endguest
            </div>
        </nav>
    </div>
</header>
