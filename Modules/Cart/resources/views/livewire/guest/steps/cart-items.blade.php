<!-- Cart Items -->
<div class="panel cart-items">
    @foreach ($cart->items as $cartItem)
        <article class="cart-item">
            @if(!isset($canChange) || $canChange == true)
                <button class="remove-item" wire:click="removeItem({{ $cartItem->id }})"
                    aria-label="@lang('core::attributes.delete')">×</button>
            @endif
            <div class="item-image">
                <img src="{{ $cartItem->product->main_image?->getThumbnailUrl('small') }}"
                    alt="{{$cartItem->product->name}}">
            </div>
            <div class="item-info">
                <h2><a href="{{ route('products.show', $cartItem->product) }}">{{$cartItem->product->name}}</a></h2>
                <span>{{formatPrice($cartItem->sku->volume_ml)}} @lang('product::attributes.ml')</span>
            </div>
            <div class="item-price">
                {{ formatPrice($cartItem->final_price * $cartItem->quantity) }} {{ $currency }}
            </div>
            <div class="qty">
                @if(!isset($canChange) || $canChange == true)
                    <button type="button"
                        wire:click="updateQuantity({{ $cartItem->id }},{{ $cartItem->quantity - 1 }})">−</button>
                    <span>{{ formatPrice($cartItem->quantity) }}</span>
                    <button type="button"
                        wire:click="updateQuantity({{ $cartItem->id }},{{ $cartItem->quantity + 1 }})">+</button>
                @else
                    <span>{{ formatPrice($cartItem->quantity) }}</span>
                @endif
            </div>
        </article>
    @endforeach
</div>
