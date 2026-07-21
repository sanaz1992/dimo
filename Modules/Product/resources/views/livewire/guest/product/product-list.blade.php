<main>
    <section class="page-header">
        <div class="container">
            <div class="breadcrumbs">
                <a href="#">@lang('shop::attributes.home_page')</a>
                <span>/</span>
                <span>@lang('product::attributes.products')</span>
                <span>/</span>
                <span> {{ $category?->name }}</span>
            </div>

            <h1 class="page-title">@lang('product::attributes.products') {{ $category?->name }}</h1>

            <div class="filter-bar">
                <div class="sort-box">
                    <button class="sort-btn">مرتب‌سازی</button>
                    <button class="sort-btn">پرفروش‌ترین</button>
                </div>

                <div class="category-tabs">
                    <a href="{{ route('products.index') }}"
                        class="tab-btn {{ request()->routeIs('products.index') ? 'active' : '' }} ">
                        @lang('shop::attributes.all')
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('categories.products.index', $category) }}"
                            class="tab-btn  {{ request()->routeIs('categories.products.index') && request()->route('category')->id === $category->id ? 'active' : '' }}">
                            {{$category->name}}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="product-grid">
                @foreach ($products as $product)
                    <article class="product-card">
                        <div class="product-badge">جدید</div>
                        <button class="wishlist-btn" aria-label="افزودن به علاقه‌مندی">♡</button>

                        <div class="product-image">
                            <img src="{{ $product->main_image?->getThumbnailUrl('small') }}" />
                        </div>

                        <div class="product-content">
                            <h3 class="product-name">{{$product->name}}</h3>
                            <div class="product-meta">
                                <div class="rating">★★★★★ <span>(4.9)</span></div>
                                <div class="price">۲۸۵,۰۰۰ تومان</div>
                            </div>
                            <div class="product-actions">
                                <button class="product-btn">افزودن به سبد خرید</button>
                                <button class="quick-view">👁</button>
                            </div>
                        </div>
                    </article>
                @endforeach

            </div>

            <div class="pagination">
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <a href="#" class="page-link">›</a>
            </div>
        </div>
    </section>
</main>

@push('styles')
    @vite('Modules/Shop/resources/assets/css/products.css')
@endpush
