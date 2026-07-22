<?php

namespace Modules\Cart\Http\Livewire;

use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Attributes\On;
use Modules\Cart\Entities\CartItem;
use Modules\Cart\Services\CartManager;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;

class CartPage extends GuestBaseComponent
{
    public $currency;

    public $cart;

    public string $step = 'cart';

    public $cartItemsCount;

    public function mount()
    {
        $this->loadCart();

        $this->step = request('step', 'cart');

        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();
    }

    public function continue()
    {
        if ($this->step === 'cart') {
            if ($this->cartItemsCount < 1) {
                return;
            }

            if (! auth()->check()) {
                session(['url.intended' => route('cart.index', ['step' => 'address'])]);

                return redirect()->route('login');
            }

            $this->step = 'address';

            return;
        }

        if ($this->step === 'auth') {
            if (! auth()->check()) {
                return;
            }

            $this->step = 'address';

            return;
        }

        if ($this->step === 'address') {
            if (! $this->selectedAddressId) {
                $this->addError('selectedAddressId', 'لطفا آدرس را انتخاب کنید.');

                return;
            }

            $this->step = 'review';
        }
    }

    #[On('cart-updated')]
    public function loadCart(): void
    {
        $this->cart = app(CartManager::class)
            ->getCart($this->currentUser());

        $this->cart?->load([
            'items.product',
            'items.sku',
        ]);

        $items = $this->cart?->items ?? collect();
        $this->cartItemsCount = (int) $items->sum('quantity');
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = $this->findCartItem($itemId);

        if ($quantity < 1) {
            $this->removeItem($itemId);

            return;
        }

        $item->update([
            'quantity' => $quantity,
        ]);

        $this->dispatch('cart-updated');

        $this->loadCart();
    }

    public function removeItem(int $itemId): void
    {
        $item = $this->findCartItem($itemId);

        $item->delete();

        $this->dispatch('cart-updated');

        $this->loadCart();
    }

    private function findCartItem(int $itemId): CartItem
    {
        abort_unless($this->cart, 404);

        return CartItem::query()
            ->where('cart_id', $this->cart->id)
            ->findOrFail($itemId);
    }

    private function currentUser(): ?Authenticatable
    {
        return auth()->user();
    }

    public function render()
    {

        $items = $this->cart?->items ?? collect();

        $subtotal = (int) $items->sum(
            fn ($item) => (int) $item->final_price * (int) $item->quantity
        );

        $shippingCost = 0;
        $total = $subtotal + $shippingCost;

        return $this->renderView(
            'Cart::livewire.guest.cart-page',
            compact('subtotal', 'total')
        )->layoutData(
            ['title' => __('product::attributes.products')]
        );
    }
}
