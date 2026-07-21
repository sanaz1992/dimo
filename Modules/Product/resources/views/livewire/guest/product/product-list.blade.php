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
                    <x-Product::guest.product-card :product="$product" :currency="$currency" />
                @endforeach
            </div>

            {{$products->links('Shop::pagination')}}

        </div>
    </section>
</main>

@push('styles')
    @vite('Modules/Shop/resources/assets/css/products.css')
@endpush
