<?php

namespace Modules\Product\External\Contracts;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;
use Modules\Product\Entities\Product;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data): Product;

    public function firstOrCreate(array $condition, array $data): Product;

    public function update(Model $product, array $data): ?Model;

}
