<?php

namespace Modules\Order\Http\Livewire\User;

use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Entities\Order;

class UserOrderShow extends UserBaseComponent
{
    use LivewireNotify;

    public $order;

    public $currency;

    public function mount(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            session()->flash(
                'error',
                __('شما اجازه دسترسی به این سفارش را ندارید.')
            );

            $this->redirectRoute(
                'orders.index',
                navigate: true
            );

            return;
        }
        $this->order = $order;
        $this->reloadOrderItems();
        $order->load('user');

        $this->currency = app(SettingHelper::class)->currencyLabel();
    }

    public function reloadOrderItems()
    {
        $this->order->load(
            'items.product_sku.product',
        );
    }

    public function render()
    {
        return $this->renderView('Order::livewire.user.order.order-show')
            ->layoutData([
                'title' => __('order::attributes.orders_show').' '.$this->order->order_number,
            ]);
    }
}
