<main>
    <section class="hero">
        <div class="container">
            <div class="" style="background-image:url('{{ asset('img/slider.jpg') }}');">
                <div class="hero-copy">
                    <div class="eyebrow">گلاب و عرقیات طبیعی</div>
                    <h1 class="hero-title">محمد</h1>
                    <div class="hero-subtitle">طبیعت خالص، طعمی اصیل</div>
                    <p class="hero-desc">
                        بهترین گلاب و عرقیات سنتی با کیفیت بالا، بسته‌بندی شیک و ارسال سریع.
                        انتخابی مطمئن برای مصرف روزانه، هدیه و سوغات.
                    </p>

                    <div class="cta-row">
                        <a class="btn btn-primary" href="{{ route('products.index') }}">
                            @lang('product::attributes.show_products')
                            <span aria-hidden="true">←</span>
                        </a>
                        <a class="btn btn-ghost" href="{{ route('about.index') }}">
                            @lang('pages::attributes.about')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature">
                    <div class="f-icon">🛡️</div>
                    <div>
                        <h4>طبیعی و کیفیت</h4>
                        <p>گارانتی محصول و بازگشت</p>
                    </div>
                </div>

                <div class="feature">
                    <div class="f-icon">🎁</div>
                    <div>
                        <h4>بسته‌بندی ویژه</h4>
                        <p>مناسب هدیه و سوغات</p>
                    </div>
                </div>

                <div class="feature">
                    <div class="f-icon">🚚</div>
                    <div>
                        <h4>ارسال سریع</h4>
                        <p>ارسال به سراسر کشور</p>
                    </div>
                </div>

                <div class="feature">
                    <div class="f-icon">✔️</div>
                    <div>
                        <h4>تضمین کیفیت</h4>
                        <p>گارانتی بازگشت وجه</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>دسته‌بندی محصولات</h2>
                <div class="line"></div>
            </div>

            <div class="categories-grid">
                @foreach ($categories as $category)
                    <article class="category-card">
                        <div class="cat-illus">
                            <img src="{{ $category->main_image?->getThumbnailUrl('small') }}" />
                        </div>
                        <h3>{{$category->name}}</h3>
                        <a href="{{ route('categories.products.index', $category) }}">
                            @lang('shop::attributes.show')
                            <span>←</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>محصولات ویژه</h2>
                <div class="line"></div>
            </div>

            <div class="products-grid">
                @foreach ($products as $product)
                    <x-Product::guest.product-card :product="$product" :currency="$currency" />
                @endforeach
            </div>
        </div>
    </section>
</main>
