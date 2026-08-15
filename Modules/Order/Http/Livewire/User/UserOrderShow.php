<?php

namespace Modules\Order\Http\Livewire\User;

use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\User\UserBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Entities\Order;
use Modules\Order\Services\OrderProductionService;

class UserOrderShow extends UserBaseComponent
{
    use LivewireNotify;

    public $order;

    public $totalSteps;

    public $doneSteps;

    public $fabrics = [];

    public $currency;

    public function mount(Order $order)
    {
        dd($order);
        $this->order = $order;
        [$this->totalSteps, $this->doneSteps] = resolve(OrderProductionService::class)
            ->calculateProductionProgress($order);
        $this->reloadOrderItems();

        $this->currency = app(SettingHelper::class)->currencyLabel();
    }

    public function reloadOrderItems()
    {
        $this->order->load(
            'user',
            'items.product',
            'items.item_fabrics.fabric',
            'items.item_fabrics.color',
            'items.item_fabrics.product_required_fabric',
            'items.status_logs'
        );
        $this->fabrics = [];
        foreach ($this->order->items as $item) {
            foreach ($item->item_fabrics as $itemFabric) {
                $this->fabrics[$itemFabric->fabric_id] = [
                    'code' => $itemFabric->fabric->code,
                    'title' => $itemFabric->fabric->title,
                    'qty' => ($this->fabrics[$itemFabric->fabric_id]['qty'] ?? 0) + $itemFabric->qty,
                    'price' => $itemFabric->fabric_price,
                ];
            }
        }
    }

    public function render()
    {
        return $this->renderView('Order::livewire.order.seller.seller-order-show')
            ->layoutData([
                'title' => __('order::attributes.orders_show'),
            ]);
    }
}
