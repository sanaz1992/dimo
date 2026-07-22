<a class="icon-btn" href="{{ route('cart.index') }}" aria-label="@lang('shop::attributes.cart')">
    <span class="badge">{{ $cartItemsCount }}</span>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 6h15l-1.5 8h-11z"></path>
        <path d="M6 6l-2-3H2"></path>
        <circle cx="9" cy="20" r="1.5"></circle>
        <circle cx="18" cy="20" r="1.5"></circle>
    </svg>
</a>
