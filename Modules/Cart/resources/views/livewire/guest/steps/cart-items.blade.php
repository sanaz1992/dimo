<!-- Cart Items -->
<div class="panel cart-items">
    @foreach ($cart->items as $cartItem)
        <article class="cart-item">
            <button class="remove-item" wire:click="removeItem({{ $cartItem->id }})" aria-label="حذف محصول">×</button>
            <div class="item-image">
                <img src="{{ $cartItem->product->main_image?->getThumbnailUrl('small') }}"
                    alt="{{$cartItem->product->name}}">
            </div>
            <div class="item-info">
                <h2>{{$cartItem->product->name}}</h2>
                <span>{{formatPrice($cartItem->sku->volume_ml)}} @lang('product::attributes.ml')</span>
            </div>
            <div class="item-price">
                {{ formatPrice($cartItem->final_price * $cartItem->quantity) }} {{ $currency }}
            </div>
            <div class="qty">
                <button type="button">−</button>
                <span>{{ formatPrice($cartItem->quantity) }}</span>
                <button type="button">+</button>
            </div>
        </article>
    @endforeach
</div>
