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
                <article class="category-card">
                    <div class="cat-illus">
                        <div class="shape"></div>
                        <div class="drop"></div>
                    </div>
                    <h3>پک هدیه</h3>
                    <a href="#">مشاهده <span>←</span></a>
                </article>

                <article class="category-card">
                    <div class="cat-illus">
                        <div class="shape"></div>
                        <div class="drop" style="height:96px;background:linear-gradient(180deg,#cfc1a5,#7f5b36);">
                        </div>
                        <div class="leaf" style="left:8px;transform:rotate(-18deg);"></div>
                    </div>
                    <h3>دمنوش ها</h3>
                    <a href="#">مشاهده <span>←</span></a>
                </article>

                <article class="category-card">
                    <div class="cat-illus">
                        <div class="shape"></div>
                        <div class="drop" style="background:linear-gradient(180deg,#c7b08a,#8d6845);"></div>
                        <div class="leaf" style="background:linear-gradient(135deg,#47a35e,#2a6b40);"></div>
                    </div>
                    <h3>عرق کاسنی</h3>
                    <a href="#">مشاهده <span>←</span></a>
                </article>

                <article class="category-card">
                    <div class="cat-illus">
                        <div class="shape"></div>
                        <div class="drop" style="background:linear-gradient(180deg,#d9c2a0,#7f5731);"></div>
                        <div class="leaf" style="background:linear-gradient(135deg,#3da05f,#1f5e36);"></div>
                    </div>
                    <h3>عرق بیدمشک</h3>
                    <a href="#">مشاهده <span>←</span></a>
                </article>

                <article class="category-card">
                    <div class="cat-illus">
                        <div class="shape"></div>
                        <div class="drop" style="background:linear-gradient(180deg,#d4b98b,#8c5f32);"></div>
                        <div class="leaf" style="background:linear-gradient(135deg,#3aa25d,#184d2e);"></div>
                    </div>
                    <h3>عرق نعناع</h3>
                    <a href="#">مشاهده <span>←</span></a>
                </article>

                <article class="category-card">
                    <div class="cat-illus">
                        <div class="shape"></div>
                        <div class="drop" style="background:linear-gradient(180deg,#ecd6af,#8e6537);"></div>
                        <div class="leaf" style="background:linear-gradient(135deg,#46ab62,#22583b);"></div>
                    </div>
                    <h3>گلاب</h3>
                    <a href="#">مشاهده <span>←</span></a>
                </article>
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
                <article class="product-card">
                    <button class="wishlist" aria-label="علاقه‌مندی">♡</button>
                    <div class="product-image">
                        <div class="sprigs"></div>
                        <div class="mini-bottle"></div>
                    </div>
                    <h3 class="product-name">گلاب ممتاز کاشان</h3>
                    <div class="rating">★ ★ ★ ★ ★</div>
                    <div class="price">۳۸۵,۰۰۰ تومان</div>
                    <button class="product-btn">افزودن به سبد خرید</button>
                </article>

                <article class="product-card">
                    <button class="wishlist" aria-label="علاقه‌مندی">♡</button>
                    <div class="product-image">
                        <div class="sprigs"></div>
                        <div class="mini-bottle"
                            style="background:linear-gradient(180deg,#f2eadc,#b98f60 62%,#7d4d28);">
                        </div>
                    </div>
                    <h3 class="product-name">عرق نعناع خالص</h3>
                    <div class="rating">★ ★ ★ ★ ★</div>
                    <div class="price">۲۴۰,۰۰۰ تومان</div>
                    <button class="product-btn">افزودن به سبد خرید</button>
                </article>

                <article class="product-card">
                    <button class="wishlist" aria-label="علاقه‌مندی">♡</button>
                    <div class="product-image">
                        <div class="sprigs"></div>
                        <div class="mini-bottle"
                            style="background:linear-gradient(180deg,#efe4d1,#b08555 62%,#68401f);">
                        </div>
                    </div>
                    <h3 class="product-name">پک هدیه لوکس</h3>
                    <div class="rating">★ ★ ★ ★ ★</div>
                    <div class="price">۶۹۵,۰۰۰ تومان</div>
                    <button class="product-btn">افزودن به سبد خرید</button>
                </article>

                <article class="product-card">
                    <button class="wishlist" aria-label="علاقه‌مندی">♡</button>
                    <div class="product-image">
                        <div class="sprigs"></div>
                        <div class="mini-bottle"
                            style="background:linear-gradient(180deg,#f1e6d6,#ab7d50 62%,#744423);">
                        </div>
                    </div>
                    <h3 class="product-name">عرق بیدمشک اصل</h3>
                    <div class="rating">★ ★ ★ ★ ★</div>
                    <div class="price">۲۹۰,۰۰۰ تومان</div>
                    <button class="product-btn">افزودن به سبد خرید</button>
                </article>
            </div>
        </div>
    </section>
</main>