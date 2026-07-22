<?php

namespace Modules\Product\External\Contracts;

use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;
use Modules\Product\Entities\ProductSku;

interface ProductSkuRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data): ProductSku;

    public function findProductSku(int $productId, int $skuId): ?ProductSku;
}
