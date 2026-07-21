<main>
    <section class="page-header">
        <div class="container">
            <div class="breadcrumbs">
                <a href="#">@lang('shop::attributes.home_page')</a>
                <span>/</span>
                <span>@lang('product::attributes.products')</span>
            </div>

            <h1 class="page-title">@lang('product::attributes.products')</h1>

            <div class="filter-bar">
                <div class="sort-box">
                    <button class="sort-btn">مرتب‌سازی</button>
                    <button class="sort-btn">پرفروش‌ترین</button>
                </div>

                <div class="category-tabs">
                    <button class="tab-btn active">@lang('shop::attributes.all')</button>
                    @foreach ($categories as $category)
                        <button class="tab-btn">{{$category->name}}</button>
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
                            <h3 class="product-name">{{$product->name   }}</h3>
                            <div class="product-meta">
                                <div class="rating">★★★★★ <span>(4.9)</span></div>
                                <div class="price">۲۸۵,۰۰۰ تومان</div>
                            </div>
                            <div class="product-actions">
                                <button class="add-to-cart">افزودن به سبد خرید</button>
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
