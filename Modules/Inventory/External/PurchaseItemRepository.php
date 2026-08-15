<?php

namespace Modules\Inventory\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Inventory\Entities\PurchaseItem;
use Modules\Inventory\External\Contracts\PurchaseItemRepositoryInterface;

class PurchaseItemRepository extends BaseRepository implements PurchaseItemRepositoryInterface
{
    public function __construct(PurchaseItem $model)
    {
        parent::__construct($model);
    }
}
