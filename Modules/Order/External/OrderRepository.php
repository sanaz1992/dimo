<?php

namespace Modules\Order\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Order\Entities\Order;
use Modules\Order\External\Contracts\OrderRepositoryInterface;
use Modules\Shipment\Entities\Shipment;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function changeStatus(Order $order, string $status): Order
    {
        $order->status = $status;
        $order->save();
        return $order;
    }

    public function allItemsHaveStatus(Order $order, array $statuses): mixed
    {
        $order->load(['items' => function ($q) use ($statuses) {
            $q->whereIn('status', $statuses);
        }]);
        return $order->items;
    }
}
