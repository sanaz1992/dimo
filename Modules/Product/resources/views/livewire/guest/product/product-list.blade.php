<main>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>@lang('product::attributes.products') </h2>
                <div class="line"></div>
            </div>

            <div class="products-grid">
                @foreach ($products as $product)
                    <article class="product-card">
                        <button class="wishlist" aria-label="علاقه‌مندی">♡</button>
                        <div class="product-image">
                            <img src="{{ $product->main_image?->getThumbnailUrl('small') }}" />
                        </div>
                        <h3 class="product-name">{{$product->name}}</h3>
                        <div class="rating">★ ★ ★ ★ ★</div>
                        <div class="price">۳۸۵,۰۰۰ تومان</div>
                        <button class="product-btn">افزودن به سبد خرید</button>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</main>

@push('styles')
    @vite( 'Modules/Shop/resources/assets/css/products.css')
@endpush
