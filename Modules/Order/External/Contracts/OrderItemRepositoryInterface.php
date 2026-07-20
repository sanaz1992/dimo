<?php

namespace Modules\Order\External\Contracts;

use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;
use Modules\Order\Entities\OrderItemFabric;

interface OrderItemRepositoryInterface extends BaseRepositoryInterface
{
    public function createItemFabric(array $data): OrderItemFabric;

    public function updateItemsStatus(array $orderItemsId, string $status): void;
}
