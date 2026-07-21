<header class="topbar">
    @php
        $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class);
    @endphp
    <div class="container">
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
                <button class="icon-btn" aria-label="@lang('shop::attributes.cart')">
                    <span class="badge">2</span>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 6h15l-1.5 8h-11z"></path>
                        <path d="M6 6l-2-3H2"></path>
                        <circle cx="9" cy="20" r="1.5"></circle>
                        <circle cx="18" cy="20" r="1.5"></circle>
                    </svg>
                </button>
            </div>
        </nav>
    </div>
</header>
