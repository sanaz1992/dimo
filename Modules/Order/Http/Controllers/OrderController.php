<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Factory\Entities\ProductionOrderItem;
use Modules\Factory\Enums\ProductionOrderItemStatus;
use Modules\Factory\External\Contracts\ProductionOrderItemRepositoryInterface;
use Modules\Factory\Services\HallService;
use Modules\Factory\Services\ProductionOrderItemService;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected ProductionOrderItemService $productionOrderItemService,
        protected HallService $hallService
    ) {
    }

    public function doneAllOrders()
    {
        $productionOrderItems = $this->productionOrderItemService->list(
            orderBy: 'sort:asc',
            with: ['product_hall_difination', 'steps']
        );
        foreach ($productionOrderItems->where('status', ProductionOrderItemStatus::PENDING->value) as $productionOrderItem) {
            $hall = $this->hallService->find($productionOrderItem->product_hall_difination->hall_id);
            $hall->load('production_processes.steps', 'stations');
            $this->productionOrderItemService
                ->createSteps($productionOrderItem, ['station_id' => $hall->stations->first()->id]);
        }
        foreach ($productionOrderItems->where('status', ProductionOrderItemStatus::PLANNED->value) as $productionOrderItem) {
            $this->productionOrderItemService->finishAllSteps($productionOrderItem);
        }
        foreach ($productionOrderItems->where('status', ProductionOrderItemStatus::IN_PROGRESS->value) as $productionOrderItem) {
            $this->productionOrderItemService->finishAllSteps($productionOrderItem);
        }
        $productionOrderItems = $productionOrderItems->where('status', ProductionOrderItemStatus::Done->value);
        $orderItemsId = $productionOrderItems->pluck('order_item_id')->unique();
        $orders = $this->orderService->list(conditions: [
            'whereHas' => ['items' => function ($q) use ($orderItemsId) {
                $q->whereIn('id', $orderItemsId);
            }]
        ]);
        foreach ($orders->where('status', OrderStatus::PRODUCED->value) as $order) {
            $order->status = OrderStatus::SHIPPED->value;
            $order->save();
        }
        foreach ($orders->where('status', OrderStatus::IN_PRODUCTION->value) as $order) {
            $order->status = OrderStatus::SHIPPED->value;
            $order->save();
        }
        $orders = $this->orderService->list();
        foreach ($orders->where('status', OrderStatus::SHIPPED->value) as $order) {
            $order->status = OrderStatus::DELIVERED->value;
            $order->save();
        }
        dd($orders);
    }

    public function invoice(\Modules\Order\Entities\Order $order)
    {
        $order->load(
            'user',
            'items.product',
            'items.item_fabrics.fabric',
            'items.item_fabrics.color',
            'items.item_fabrics.product_required_fabric'
        );

        return view('Order::invoice.print', compact('order'));
    }
}
