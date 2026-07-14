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

    public function addMaterials(Product $product, array $materials): void;

    public function deleteMaterial(Product $product, int $materialId): void;

    public function addIntermediateProductMaterials(Product $product, array $data): void;

    public function deleteIntermediateProductMaterial(Product $product, int $materialId): void;

    public function createProductHallDefine(Product $product, int $hallId, ?int $sort = null): void;

    public function removeProductHallDefine(Product $product, int $stateId): void;

    public function publishProduct(Product $product, ?bool $publish = null): Product;

    public function addFabric(Product $product, array $data);

    public function deleteProductFabric(int $productFabricId);

    public function updateCostItems(Product $product, array $data): Product;
}
