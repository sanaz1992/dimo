<?php

namespace Modules\Order\External\Contracts;

use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;
use Modules\Order\Entities\Order;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function changeStatus(Order $order, string $status): Order;
    public function allItemsHaveStatus(Order $order, array $statuses): mixed;
}
