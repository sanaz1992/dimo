<main class="page">
    <div class="container">
        <div class="breadcrumb">
            <x-Shop::breadcrumbs :items="[
        ['label' => __('shop::attributes.home_page'), 'url' => route('home')],
        ['label' => __('shop::attributes.cart')],
    ]" />
        </div>

        <section class="{{ $cart->items->count() ? 'grid grid-cols-2' : '' }} cart-layout">

            @if ($cart->items->count())
                <div>
                    @if ($step === 'cart')
                        @include('Cart::livewire.guest.steps.cart-items')
                        {{-- @elseif ($step === 'auth')
                        @include('Cart::livewire.guest.steps.auth') --}}
                    @elseif ($step === 'address')
                        @include('Cart::livewire.guest.steps.address')
                    @elseif ($step === 'review')
                        @include('Cart::livewire.guest.steps.review')
                    @endif
                </div>
                <div>
                    <!-- Summary -->
                    <aside class="panel summary">
                        <h3>@lang('cart::attributes.cart_summery')</h3>

                        <div class="summary-row">
                            <span>{{formatPrice($cartItemsCount)}} @lang('cart::attributes.products_count')</span>
                            <span>{{formatPrice($subtotal)}} {{ $currency }}</span>
                        </div>

                        <div class="summary-row">
                            <span>@lang('cart::attributes.shipping_cost')</span>
                            <span class="free">@lang('cart::attributes.free')</span>
                        </div>

                        <div class="summary-total">
                            <span>@lang('cart::attributes.total')</span>
                            <span>{{formatPrice($total)}} {{ $currency }}</span>
                        </div>

                        @if ($step != 'review')
                            <button class="checkout-btn"
                                wire:click="continue">@lang('cart::attributes.continue_shopping')</button>
                        @else
                            <button wire:click="redirectToPayment" wire:loading.attr="disabled" class="checkout-btn">
                                <span wire:loading.remove
                                    wire:target="redirectToPayment">@lang('cart::attributes.place_order')</span>
                                <span wire:loading
                                    wire:target="redirectToPayment">@lang('cart::messages.redirecting_to_gateway')...</span>
                            </button>
                        @endif
                    </aside>
                </div>
            @else
                <div class="empty-cart">
                    <div class="empty-cart__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="19" cy="20" r="1"></circle>
                            <path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.5h7.8a2 2 0 0 0 2-1.6L21 7H6"></path>
                        </svg>
                    </div>

                    <h2 class="empty-cart__title">
                        @lang('cart::messages.your_cart_is_empty')
                    </h2>

                    <p class="empty-cart__description">
                        @lang('cart::messages.you_have_no_items_in_your_shopping_cart')
                    </p>

                    <a href="{{ route('products.index') }}" class="empty-cart__button">
                        @lang('cart::attributes.show_products')
                    </a>
                </div>
            @endif

        </section>
    </div>
</main>

@push('styles')
    @vite('Modules/Cart/resources/assets/css/cart.css')
@endpush
