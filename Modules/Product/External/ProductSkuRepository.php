<?php

namespace Modules\Product\External;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Product\Entities\ProductSku;
use Modules\Product\External\Contracts\ProductSkuRepositoryInterface;

class ProductSkuRepository extends BaseRepository implements ProductSkuRepositoryInterface
{
    public function __construct(ProductSku $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): ProductSku
    {
        $data['sku'] = $data['sku'] ?? CodeGeneratorHelper::generate(get_class(new ProductSku), 'sku');

        return ProductSku::create($data);
    }

    public function findProductSku(int $productId, int $skuId): ?ProductSku
    {
        return ProductSku::query()
            ->where('product_id', $productId)
            ->where('id', $skuId)
            ->first();
    }
}
