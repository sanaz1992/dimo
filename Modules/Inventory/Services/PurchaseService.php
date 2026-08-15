<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Traits\LivewireNotify;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Enums\PurchaseStatus;
use Modules\Inventory\External\Contracts\PurchaseRepositoryInterface;

class PurchaseService
{
    use LivewireNotify;

    public function __construct(
        protected PurchaseRepositoryInterface $purchaseRepository,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->purchaseRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function find($id)
    {
        return $this->purchaseRepository->find($id);
    }

    public function create(array $data): Purchase
    {
        $data['created_by'] = auth()->id();

        return DB::transaction(function () use ($data) {
            $purchase = $this->purchaseRepository->create($data);

            return $purchase;
        });
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $purchase = $this->purchaseRepository->update($purchase, $data);
            if ($purchase->status == PurchaseStatus::RECEIVED->value) {
                $this->receivedPurchase($purchase);
            }

            return $purchase;
        });
    }

    public function receivedPurchase(Purchase $purchase)
    {
        $purchase->load('items.product_sku');
        foreach ($purchase->items as $purchaseItem) {
            $productSku = $purchaseItem->product_sku;
            $productSku->stock += $purchaseItem->quantity;
            $productSku->price = $purchaseItem->sale_price;
            $productSku->save();
        }
    }
}
