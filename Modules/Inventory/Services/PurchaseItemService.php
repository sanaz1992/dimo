<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Traits\LivewireNotify;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Entities\PurchaseItem;
use Modules\Inventory\Enums\PurchaseStatus;
use Modules\Inventory\External\Contracts\PurchaseItemRepositoryInterface;
use Modules\Product\External\ProductSkuRepository;

class PurchaseItemService
{
    use LivewireNotify;

    public function __construct(
        protected PurchaseItemRepositoryInterface $purchaseItemRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->purchaseItemRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function find($id)
    {
        return $this->purchaseItemRepository->find($id);
    }

    public function create(Purchase $purchase, array $data): PurchaseItem
    {
        $data['purchase_id'] = $purchase->id;

        return DB::transaction(function () use ($purchase, $data) {
            $productSku = app(ProductSkuRepository::class)->firstOrCreate([
                'product_id' => $data['product_id'],
                'packaging_type' => $data['packaging_type'],
                'volume_ml' => $data['volume_ml'],
            ], [
                'price' => $data['sale_price'],
                'stock' => $purchase->status == PurchaseStatus::RECEIVED->value ? $data['quantity'] : 0,
            ]);

            $data['product_sku_id'] = $productSku->id;
            $data['total_cost'] = $data['purchase_price'] * $data['quantity'];
            $purchaseItem = $this->purchaseItemRepository->create($data);
            if ($purchase->status == PurchaseStatus::RECEIVED->value && $productSku->price != $data['sale_price']) {
                app(ProductSkuRepository::class)->update($productSku, [
                    'stock' => $productSku->stock + $purchaseItem->quantity,
                    'price' => $data['sale_price'],
                ]);
            }

            return $purchaseItem;
        });
    }

    public function update(PurchaseItem $purchaseItem, array $data): PurchaseItem
    {
        return DB::transaction(function () use ($purchaseItem, $data) {
            $purchaseItem = $this->purchaseItemRepository->update($purchaseItem, $data);

            return $purchaseItem;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $this->purchaseItemRepository->delete($id);

            return true;
        });
    }
}
