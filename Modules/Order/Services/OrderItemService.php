<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Enums\OrderItemStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\External\OrderItemRepository;
use Modules\Product\Services\ProductService;

class OrderItemService
{
    public function __construct(
        protected OrderItemRepository $orderItemRepository,
        protected OrderItemFabricService $orderItemFabricService,
        protected ProductService $productService
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [])
    {
        return $this->orderItemRepository->all($orderBy, $limit, $with, $conditions);
    }

    public function find($id)
    {
        return $this->orderItemRepository->find($id);
    }
    // public function attachProducts(Order $order, array $productsData): array
    // {
    //     $subTotal = 0;
    //     $totalPrice = 0;

    //     foreach ($productsData as $productData) {
    //         [$productPrice, $totalProductPrice] = $this->createOrderItem($order, $productData);

    //         $subTotal += $productPrice;
    //         $totalPrice += $totalProductPrice;
    //     }

    //     return [$subTotal, $totalPrice];
    // }

    public function createOrderItem(Order $order, array $data): OrderItem
    {
        $orderItem = $this->orderItemRepository->create([
            'order_id' => $order->id,
            'product_id' => $data['product_id'],
            'qty' => $data['qty'],
            'status' => $data['status'] ?? OrderItemStatus::DRAFT->value,
            'custom_frame' => $data['custom_frame'] ?? false,
            'send_fabric_by_customer' => $data['send_fabric_by_customer'] ?? false,
            'frame_color_id' => $data['frame_color_id'] ?? null,
            'price' => 0,
            'total_price' => 0,
        ]);

        $product = $this->productService->find($data['product_id']);

        $totalFabricPrice = $this->orderItemFabricService->attachFabrics($orderItem, $product, $data);

        $productPrice = $product->effective_price + $totalFabricPrice;
        $totalProductPrice = $this->calculateProductTotal($orderItem, $productPrice);

        $orderItem->update([
            'price' => $productPrice,
            'total_price' => $totalProductPrice,
        ]);

        resolve(OrderService::class)->updateOrderPrices($orderItem->order_id);

        return $orderItem;
    }

    protected function calculateProductTotal($orderItem, float $productPrice): float
    {
        $totalProductPrice = $productPrice * $orderItem->qty;

        if ($orderItem->custom_frame) {
            $totalProductPrice += $totalProductPrice * 0.10;
        }

        return $totalProductPrice;
    }

    public function update(OrderItem $orderItem, array $data)
    {
        $this->orderItemRepository->update($orderItem, $data);
    }

    public function updateItemsStatus(Order $order, string $status)
    {
        if (! OrderItemStatus::tryFrom($status)) {
            throw new \InvalidArgumentException("وضعیت مورد نظر معتبر نمیباشد: {$status}");
        }
        foreach ($order->items as $item) {
            $item->status = $status;
            $item->save();
        }

        return $order;
    }

    public function removeOrderItem(int $orderItemId)
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);
        $orderId = $orderItem->order_id;
        $this->orderItemRepository->delete($orderItemId);

        return resolve(OrderService::class)->updateOrderPrices($orderId);
    }

    public function checkQuality(int $orderItemId, bool $accepted)
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);

        return DB::transaction(function () use ($orderItem, $accepted) {
            if ($accepted) {
                $this->orderItemRepository->update($orderItem, ['status' => OrderItemStatus::QUALITY_APPROVED->value]);
                $status = true;
            } else {
                $this->orderItemRepository->update($orderItem, ['status' => OrderItemStatus::QUALITY_REJECTED->value]);
                $status = false;
                // todo:: create new order item and produce again for this order
            }
            $order = resolve(OrderService::class)->find($orderItem->order_id);
            $order->load('items');
            if ($order->items->count() == $order->items->where('status', OrderItemStatus::QUALITY_APPROVED->value)->count()) {
                resolve(OrderService::class)->update($order, ['status' => OrderStatus::PACKAGING->value]);
            }

            return [
                'status' => true,
                'message' => $status ?
                    __('order::messages.order_item_quality_approved') :
                    __('order::messages.order_item_quality_rejected'),
            ];
        });
    }

    public function packaged($orderItemId)
    {
        $orderItem = $this->orderItemRepository->find($orderItemId);

        return DB::transaction(function () use ($orderItem) {
            $orderItem = $this->orderItemRepository->update($orderItem, ['status' => OrderItemStatus::PACKAGED->value]);

            $order = resolve(OrderService::class)->find($orderItem->order_id);
            $order->load('items');
            if ($order->items->count() == $order->items->where('status', OrderItemStatus::PACKAGED->value)->count()) {
                resolve(OrderService::class)->update($order, ['status' => OrderStatus::AWAITING_SHIPPED->value]);
            }

            return $orderItem;
        });
    }
}
