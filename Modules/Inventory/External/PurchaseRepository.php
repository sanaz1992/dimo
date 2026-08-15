<?php

namespace Modules\Inventory\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\External\Contracts\PurchaseRepositoryInterface;

class PurchaseRepository extends BaseRepository implements PurchaseRepositoryInterface
{
    public function __construct(Purchase $model)
    {
        parent::__construct($model);
    }
}
