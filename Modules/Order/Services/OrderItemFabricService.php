<?php

namespace Modules\Order\Services;

use Modules\Order\External\OrderItemRepository;
use Modules\Warehouse\Services\FabricWarehouseService;

class OrderItemFabricService
{
    public function __construct(protected OrderItemRepository $orderItemRepository) {}

    public function attachFabrics($orderItem, $product, array $productData): float
    {
        $totalFabricPrice = 0;

        if ($product->has_fabric) {
            $product->load('required_fabrics');

            foreach ($productData['fabrics'] ?? [] as $requiredFabricId => $fabricDetail) {
                $qty = optional($product->required_fabrics->firstWhere('id', $requiredFabricId))->qty ?? 0;

                $fabric = resolve(FabricWarehouseService::class)->find($fabricDetail['fabric_id']);

                $this->orderItemRepository->createItemFabric([
                    'order_item_id' => $orderItem->id,
                    'fabric_id' => $fabricDetail['fabric_id'],
                    // 'color_id'      => $fabricDetail['color_id'],
                    'qty' => $qty * $orderItem->qty,
                    'fabric_price' => $fabric->price,
                    'product_required_fabric_id' => $requiredFabricId,
                ]);

                $totalFabricPrice += $fabric->price * $qty;
            }
        }

        return $totalFabricPrice;
    }
}
