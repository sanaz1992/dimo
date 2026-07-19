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
                <a class="active" href="{{ route('home') }}">صفحه اصلی</a>
                <a href="{{ route('products.index') }}">محصولات</a>
                <a href="#">درباره ما</a>
                <a href="#">وبلاگ</a>
                <a href="#">تماس با ما</a>
            </div>

            <div class="actions">
                <button class="icon-btn" aria-label="جستجو">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M20 20l-3.5-3.5"></path>
                    </svg>
                </button>
                <button class="icon-btn" aria-label="سبد خرید">
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