<?php

namespace Modules\Order\Http\Livewire\Admin;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Factory\Enums\ProductionOrderItemStepStatus;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Services\OrderService;

class OrderTrackingShow extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use LivewireNotify;

    public $order;

    public $form = [
        'order_code' => '',
        'product_id' => '',
    ];

    public $selectedProductId;

    public function mount(Order $order)
    {
        // $this->authorize('order_tracking');
        $this->order = $order;
        $this->form['order_code'] = $order->code;
        $this->reloadOrderItems();
        $this->selectedProductId = null;
    }

    public $productionOrderItems;

    public function reloadOrderItems($productId = null)
    {
        $this->order->load([
            'items.product.mainImageRelation',
            'items.production_order_items',
            'items.production_order_items.steps.hall_process_step',
            'items.production_order_items.product_hall_difination.hall.production_processes',
            'items.production_order_items.steps.histories' => function ($q) {
                $q->where([['field', 'status']])
                    ->whereIn('new_value', [
                        ProductionOrderItemStepStatus::STOPED->value,
                        ProductionOrderItemStepStatus::SKIPPED->value,
                    ]);
            },
        ]);
        // $this->productionOrderItems = resolve(ProductionOrderItemService::class)->list(
        //     null,
        //     [],
        //     [
        //         'steps.hall_process_step',
        //         'product_hall_difination.hall.production_processes',
        //         'steps.histories' => function ($q) {
        //             $q->where([['field', 'status'], ['new_value', 'stoped']]);
        //         }
        //     ],
        //     [
        //         'whereIn' => [
        //             'order_item_id' => [$this->order->items->pluck('id')->toArray()]
        //         ]
        //     ]
        // )->groupBy('group_name');
        // dd($this->order->items);
    }

    public function changeOrder($orderCode)
    {
        $this->order = resolve(OrderService::class)->findByColumn('code', $orderCode);
    }

    public function changeProduct($productId)
    {
        $this->selectedProductId = $productId;
    }

    public function render()
    {
        $conditions = [
            'whereIn' => [
                'status' => [[OrderStatus::APPROVED->value, OrderStatus::PAUSED->value, OrderStatus::IN_PRODUCTION]],
            ],
        ];
        $orders = resolve(OrderService::class)->list(null, [], [], $conditions);

        return $this->renderView(
            'Order::livewire.order.admin.order-tracking-show',
            compact('orders')
        )->layoutData([
            'title' => __('order::attributes.orders_view_production_process'),
        ]);
    }
}
