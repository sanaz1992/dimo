<?php

namespace Modules\Product\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Product\Entities\CostItem;
use Modules\Product\External\Contracts\CostItemRepositoryInterface;

class CostItemRepository extends BaseRepository implements CostItemRepositoryInterface
{
    public function __construct(CostItem $model)
    {
        parent::__construct($model);
    }
}
