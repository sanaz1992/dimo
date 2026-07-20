<?php

namespace Modules\Order\Http\Livewire\Traits;

use Livewire\Attributes\On;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Services\OrderService;
use Modules\User\Enums\UserLevel;

trait OrderCreateTrait
{
    use LivewireNotify;

    public $count = 0;

    public $form = [];

    #[On('first-product-added')]
    public function onFirstProductAdd($order_id)
    {
        $order = resolve(OrderService::class)->find($order_id);
        resolve(OrderService::class)->update($order, $this->form);
        $this->notify('success', __('core::messages.create.success'));
        if (auth()->user()->level == UserLevel::ADMIN->value) {
            $redirectRoute = 'admin.orders.edit';
        } else {
            $redirectRoute = 'seller.orders.edit';
        }

        return redirect()->route($redirectRoute, ['order' => $order]);
    }

    public function render()
    {
        return $this->renderView('Order::livewire.order.order-create')
            ->layoutData([
                'title' => __('order::attributes.new_order'),
            ]);
    }
}
