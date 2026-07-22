<?php

namespace Modules\Cart\Http\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Cart\Services\CartManager;

class CartIcon extends Component
{
    // گوش دادن به رویداد افزودن به سبد خرید برای به‌روزرسانی خودکار هدر
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // فقط برای trigger شدن rerender
    }

    public function render(CartManager $cartManager)
    {
        $cartItemsCount = $cartManager->getCartItemsCount(auth()->user());

        return view('Cart::livewire.guest.cart-icon', [
            'cartItemsCount' => $cartItemsCount,
        ]);
    }
}
