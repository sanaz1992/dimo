<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Cart\Entities\CartItem;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Enums\OrderItemStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\External\OrderItemRepository;
use Modules\Product\Entities\ProductSku;
use Modules\Product\Services\ProductService;

class OrderItemService
{
    public function __construct(
        protected OrderItemRepository $orderItemRepository,
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

    public function createOrderItem(Order $order, CartItem $cartItem): OrderItem|bool
    {
        return DB::transaction(function () use ($order, $cartItem) {
            $productSku = $this->productService->checkProductHasStock($cartItem->product_sku_id, $cartItem->quantity);
            if ($productSku instanceof ProductSku) {
                $orderItem = $this->orderItemRepository->create([
                    'order_id' => $order->id,
                    'product_sku_id' => $cartItem->product_sku_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $productSku->price,
                    'discount' => 0,
                    'total' => $productSku->price * $cartItem->quantity,
                ]);
            } else {
                return false;
            }

            return $orderItem;
        });
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
