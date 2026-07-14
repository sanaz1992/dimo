<?php

namespace Modules\Order\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Entities\OrderItemFabric;
use Modules\Order\External\Contracts\OrderItemRepositoryInterface;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }

    public function createItemFabric(array $data): OrderItemFabric
    {
        return OrderItemFabric::create($data);
    }

    public function delete($id)
    {
        $item = $this->model->find($id);
        if (! $item) {
            return false;
        }
        $item->item_fabrics()->delete();
        return $item->delete();
    }

    public function updateItemsStatus(array $orderItemsId, string $status): void
    {
        $orderItems = OrderItem::whereIn('id', $orderItemsId)->get();
        foreach ($orderItems as $orderItem) {
            $orderItem->status = $status;
            $orderItem->save();
        }
    }
}
