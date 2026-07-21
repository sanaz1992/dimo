<main>
    <div class="container">
        <div class="breadcrumb-wrap">
            <x-Shop::breadcrumbs :items="[
        ['label' => __('shop::attributes.home_page'), 'url' => route('home')],
        ['label' => __('product::attributes.products'), 'url' => route('products.index')],
        ['label' => $product->name],
    ]" />
        </div>

        <section class="product-detail">
            <div class="product-card">
                <div class="product-layout">

                    <!-- Gallery -->
                    <div class="gallery">
                        <div class="thumbs">
                            <div class="thumb active">
                                <img src="{{ $product->main_image?->getThumbnailUrl('small') }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" />
                            </div>
                            <div class="thumb">
                                <img src="product-2.jpg" alt="گلاب ممتاز کاشان">
                            </div>
                            <div class="thumb">
                                <img src="product-3.jpg" alt="گلاب ممتاز کاشان">
                            </div>
                        </div>

                        <div class="main-image">
                            <img src="{{ $product->main_image?->getThumbnailUrl('small') }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" />
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="product-info">
                        <h1>{{ $product->name }}</h1>

                        <div class="rating">
                            <span>★★★★★</span>
                            <span class="reviews">(۳۵) نظر</span>
                        </div>

                        <!-- نمایش قیمت داینامیک محاسبه شده به صورت آنی -->
                        <div class="price-container">
                            @if($this->priceDetails)
                                @if($this->priceDetails->discountAmount > 0)
                                    <del class="text-gray-400 block text-sm">
                                        {{ number_format($this->priceDetails->basePrice) }} {{ $currency }}
                                    </del>
                                    <span class="badge text-red-500 text-xs font-bold bg-red-50 px-2 py-0.5 rounded">
                                        {{ $this->priceDetails->discountPercentage }}% @lang('shop::attributes.discount')
                                    </span>
                                @endif

                                <div class="font-bold text-xl mt-1">
                                    {{ number_format($this->priceDetails->finalPrice) }} {{ $currency }}
                                </div>
                            @else
                                <span class="text-red-500 font-bold">@lang('shop::attributes.unavailable')</span>
                            @endif
                        </div>

                        <p class="description">
                            {!! $product->description !!}
                        </p>

                        <!-- بخش انتخاب حجم (SKU ها) -->
                        <span class="section-label">انتخاب حجم</span>
                        <div class="options">
                            @foreach ($product->skus as $sku)
                                <button type="button" wire:click="selectSku({{ $sku->id }})"
                                    class="option-btn {{ $selectedSkuId == $sku->id ? 'active' : '' }}">
                                    {{ $sku->volume_ml }} @lang('product::attributes.ml')
                                </button>
                            @endforeach
                        </div>

                        <!-- مدیریت افزودن به سبد خرید و کنترل تعداد -->
                        <div class="buy-row">
                            @if($this->priceDetails && $this->priceDetails->hasStock)
                                <div class="qty">
                                    <button type="button" wire:click="decrementQty">−</button>
                                    <span>{{ $quantity }}</span>
                                    <button type="button" wire:click="incrementQty">+</button>
                                </div>

                                <button class="add-to-cart">افزودن به سبد خرید</button>
                            @else
                                <button disabled class="add-to-cart" style="background-color: #ccc; cursor: not-allowed;">
                                    ناموجود در انبار
                                </button>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Services -->
                <section class="services">
                    <article class="service-card">
                        <div class="service-icon">↩</div>
                        <div>
                            <h4>گارانتی بازگشت وجه</h4>
                            <p>۷ روز ضمانت بازگشت</p>
                        </div>
                    </article>

                    <article class="service-card">
                        <div class="service-icon">🔒</div>
                        <div>
                            <h4>پرداخت امن</h4>
                            <p>درگاه پرداخت معتبر</p>
                        </div>
                    </article>

                    <article class="service-card">
                        <div class="service-icon">🚚</div>
                        <div>
                            <h4>ارسال سریع</h4>
                            <p>۲ تا ۴ روز کاری</p>
                        </div>
                    </article>
                </section>
            </div>
        </section>

        <!-- بخش نظرات کاربران (Comments Box) -->
        <section class="comments-section">
            <h3>نظرات کاربران</h3>

            <div class="comments-layout">
                @if(session()->has('comment_success'))
                    <div class="comment-success-alert">
                        {{ session('comment_success') }}
                    </div>
                @endif

                <!-- فرم ثبت نظر جدید -->
                <div class="comment-form-container">
                    <h4>ثبت دیدگاه جدید</h4>

                    <form wire:submit.prevent="submitComment" class="comment-form">
                        <div class="comment-form-grid">
                            <div class="comment-field-group">
                                <label>نام و نام خانوادگی *</label>
                                <input type="text" wire:model.defer="commentName" class="comment-input">
                                @error('commentName') <span class="comment-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="comment-field-group">
                                <label>امتیاز شما *</label>
                                <select wire:model.defer="commentRating" class="comment-input">
                                    <option value="5">۵ ستاره (عالی)</option>
                                    <option value="4">۴ ستاره (خوب)</option>
                                    <option value="3">۳ ستاره (معمولی)</option>
                                    <option value="2">۲ ستاره (ضعیف)</option>
                                    <option value="1">۱ ستاره (بسیار ضعیف)</option>
                                </select>
                                @error('commentRating') <span class="comment-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="comment-field-group">
                            <label>متن پیام *</label>
                            <textarea wire:model.defer="commentText" rows="4"
                                class="comment-input comment-textarea"></textarea>
                            @error('commentText') <span class="comment-error">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <button type="submit" class="btn-submit-comment">ثبت دیدگاه</button>
                        </div>
                    </form>
                </div>

                <!-- لیست نظرات ثبت شده -->
                <div class="comments-list">
                    {{-- @forelse($this->approvedComments as $comment)
                        <div class="comment-item">
                            <div class="comment-header">
                                <strong class="comment-author">{{ $comment->name }}</strong>
                                <span class="comment-stars">
                                    {{ str_repeat('★', $comment->rating) }}{{ str_repeat('☆', 5 - $comment->rating) }}
                                </span>
                            </div>
                            <p class="comment-text">
                                {{ $comment->content }}
                            </p>
                            <span class="comment-date">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @empty
                        <p class="no-comments-message">هنوز هیچ دیدگاهی برای این محصول ثبت نشده است. اولین دیدگاه را شما
                            بنویسید!</p>
                    @endforelse --}}
                </div>
            </div>
        </section>
    </div>
</main>

@push('styles')
    @vite('Modules/Shop/resources/assets/css/product-detail.css')
@endpush
