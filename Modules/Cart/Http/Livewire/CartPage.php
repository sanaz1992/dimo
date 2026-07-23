<?php

namespace Modules\Cart\Http\Livewire;

use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Modules\Cart\Entities\CartItem;
use Modules\Cart\Services\CartManager;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Guest\GuestBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Exceptions\OutOfStockException;
use Modules\Order\Services\OrderService;
use Modules\User\Entities\City;
use Modules\User\Entities\Province;
use Modules\User\External\Repositories\AddressRepository;
use Modules\User\Services\UserService;

class CartPage extends GuestBaseComponent
{
    use LivewireNotify;

    public $currency;

    public $cart;

    #[Url]
    public string $step = 'cart';

    public $cartItemsCount;

    public $user;

    public $showAddressForm = false;

    public $provinces;

    public $addressForm = [];

    public $cities = [];

    public ?int $selectedAddressId = null;

    public $selectedAddress;

    public function mount()
    {
        $this->loadCart();

        $this->step = request('step', 'cart');
        $this->checkStepData();

        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();
        if (auth()->check()) {
            $this->user = auth()->user();
            $this->user->load('addresses.city');
            if (! $this->user->addresses->count()) {
                $this->showAddressForm = true;
            }
        }

        $this->provinces = Province::orderBy('name')->get();
    }

    public function checkStepData()
    {

        switch ($this->step) {
            case 'payment':
            case 'review':
            case 'address':
                if (! $this->selectedAddressId) {
                    $this->notify('error', 'لطفا آدرس را انتخاب کنید.');

                    $this->step = 'address';
                } else {
                    $this->selectedAddress = app(AddressRepository::class)->find($this->selectedAddressId);
                }

            case 'auth':
                if (! auth()->check()) {
                    $this->step = 'cart';
                }
        }
    }

    public function provinceChanged()
    {
        $provinceId = $this->addressForm['province_id'];
        $this->cities = City::where('province_id', $provinceId)->get();
        $this->addressForm['city_id'] = null;
    }

    public function changeAddressFormStatus()
    {
        $this->showAddressForm = ! $this->showAddressForm;
    }

    protected function addressRules(): array
    {
        return [
            'addressForm.province_id' => ['required', 'exists:provinces,id'],
            'addressForm.city_id' => ['required', 'exists:cities,id'],
            'addressForm.address' => ['required', 'string', 'max:255'],
            'addressForm.postal_code' => ['required', 'string', 'max:20'],
        ];
    }

    protected function validateAddressForm()
    {
        $this->validate(
            $this->addressRules(),
            trans('cart::validation'),
            trans('cart::attributes')
        );
    }

    public function submitAddress(UserService $userService)
    {
        try {
            $userService->createAddress($this->user, $this->addressForm);
        } catch (\Exception $e) {
            report($e);
            $this->notify('error', $e->getMessage());
        }
        $this->notify('success', 'آدرس با موفقیت ثبت شد');
        reset($this->addressForm);
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
                $this->notify('error', 'لطفا آدرس را انتخاب کنید.');

                return;
            } else {
                $this->selectedAddress = app(AddressRepository::class)->find($this->selectedAddressId);
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

    public function redirectToPayment()
    {
        $cartManager = app(CartManager::class);
        // check data
        $this->step = 'payment';
        $this->checkStepData();

        // check cart items
        if (! $this->cart || $this->cart->items->isEmpty()) {
            $this->notify('error', __('cart::messages.your_cart_is_empty'));

            return;
        }

        try {
            app(OrderService::class)->createFromCart($this->cart, [
                'user_id' => auth()->id(),
                'address_id' => $this->selectedAddressId,
            ]);

            // delete cart
            $cartManager->clear();

            dd('success');
            // //send to payment
            // return PaymentService::pay($order);
        } catch (OutOfStockException $e) {
            // get item doesnt have enough stock
            $failedItem = $e->getItem();

            if ($failedItem) {
                // delete item
                $cartManager->removeItem($failedItem);
            }

            // show message
            $this->notify('notify', $e->getMessage());
        } catch (\Exception $e) {
            dd($e);
            $this->notify('error', $e->getMessage());
            $this->notify('error', __('cart::messages.error_in_submit_order'));
            $this->step = 'cart';
        }
    }
}
