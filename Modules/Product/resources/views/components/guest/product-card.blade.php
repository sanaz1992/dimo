@props(['product', 'currency'])

@php
    $price = app(\Modules\Product\Services\Pricing\ProductPriceResolver::class)->resolveForList($product);
@endphp

<article class="product-card">
    <div class="product-badge">
        @if (!$price || !$price->hasStock)
            @lang('shop::attributes.out_of_stock')
        @else
            @lang('shop::attributes.in_stock')
        @endif
    </div>

    <button class="wishlist-btn" aria-label="@lang('shop::attributes.add_to_favorites')">♡</button>

    <div class="product-image">
        <a href="{{ route('products.show', $product) }}" class="block overflow-hidden">
            <img src="{{ $product->main_image?->getThumbnailUrl('small') }}" alt="{{ $product->name }}"
                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" />
        </a>
    </div>

    <div class="product-content">
        <a href="{{ route('products.show', $product) }}" class="product-link block">
            <h3 class="product-name">{{ $product->name }}</h3>
            <div class="rating">★ ★ ★ ★ ★</div>
            <div class="price-box">
                @if($price)
                    @if($price->discountAmount > 0)
                        <del class="text-gray-400">{{ formatPrice($price->basePrice) }} {{ $currency }}</del>
                        <span class="badge text-red-500">{{ $price->discountPercentage }}%
                            @lang('shop::attributes.discount')</span>
                    @endif

                    <div class="font-bold">
                        @if($price->isFromPrice)
                            <span class="text-sm text-gray-500">@lang('shop::attributes.start_from'):</span>
                        @endif
                        {{ formatPrice($price->finalPrice) }} {{ $currency }}
                    </div>
                @endif
            </div>
        </a>
        <button class="product-btn mt-2" wire:click="addProductToCart({{ $product->id }}, {{ $price->skuId }}, 1)">@lang('shop::attributes.add_to_cart')</button>
    </div>
</article>
