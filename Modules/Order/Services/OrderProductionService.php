<?php

namespace Modules\Order\Services;

use Modules\Factory\Services\HallProductionProcessService;
use Modules\Factory\Services\ProductionOrderItemService;
use Modules\Order\Entities\Order;

class OrderProductionService
{
    public function __construct(
        protected HallProductionProcessService $hallProductionProcessService,
        protected ProductionOrderItemService $productionOrderItemService
    ) {}
    public function calculateProductionProgress(Order $order): array
    {

        $order->load('items.product.hall_difinations');

        // Retrieve all production processes along with the number of their steps
        $productionProcesses = $this->hallProductionProcessService->list()
            ->loadCount('steps')
            ->keyBy('hall_id');

        // Calculate the total number of steps required to complete the entire order
        $totalSteps = $order->items->sum(function ($item) use ($productionProcesses) {
            return $item->product->hall_difinations->sum(function ($definition) use ($item, $productionProcesses) {
                return optional($productionProcesses->get($definition->hall_id))->steps_count * $item->qty;
            });
        });

        $conditions = [
            'whereIn' => [
                'order_item_id' => [$order->items->pluck('id')->toArray()]
            ]
        ];
        $productionOrderItems = $this->productionOrderItemService->list(null, [], [], $conditions);
        $productionOrderItems->loadCount([
            'steps as done_steps_count' => fn($q) => $q->where('status', 'done')
        ]);

        // Calculate the total number of completed steps
        $doneSteps = $productionOrderItems->sum('done_steps_count');

        return [$totalSteps, $doneSteps];
    }
}
