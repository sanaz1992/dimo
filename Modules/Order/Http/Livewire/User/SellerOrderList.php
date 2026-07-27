<?php

namespace Modules\Order\Http\Livewire\Seller;

use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Seller\SellerBaseComponent;
use Modules\Order\Enums\OrderListTabs;
use Modules\Order\Services\OrderService;

class SellerOrderList extends SellerBaseComponent
{
    use WithPagination;

    public $sortBy;

    public $activeTab;

    public $tabs;

    public function mount()
    {
        $this->activeTab = OrderListTabs::PENDING->value;
        $this->tabs = OrderListTabs::labels();
        // dd($this->tabs);
    }

    public function render()
    {
        $conditions = [
            'where' => ['seller_id' => ['=', auth()->user()->id]],
            // 'whereIn' => ['status' => [OrderListTabs::statuses()[$this->activeTab]]],
        ];
        $orders = resolve(OrderService::class)->list(null, [10, true], ['user', 'items'], $conditions);

        return $this->renderView(
            'Order::livewire.order.seller.seller-order-list',
            compact('orders')
        )->layoutData([
            'title' => __('order::attributes.orders_list'),
        ]);
    }
}
