<?php

namespace Modules\Order\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Factory\Enums\ProductionOrderItemStatus;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderItemStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Enums\OrderType;
use Modules\Order\External\Contracts\OrderRepositoryInterface;
use Modules\Product\Entities\Product;
use Modules\User\Entities\User;
use Modules\User\Services\UserService;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderItemService $orderItemService,
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], ?QueryFilter $filter = null)
    {
        return $this->orderRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function find($id)
    {
        return $this->orderRepository->find($id);
    }

    public function findByColumn($col, $value)
    {
        return $this->orderRepository->findByColumn($col, $value);
    }

    public function create(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            if (! empty($orderData['customer_mobile'])) {
                $user = resolve(UserService::class)->firstOrCreate(
                    ['mobile' => $orderData['customer_mobile']],
                    [
                        'name' => $orderData['customer_name'] ?? $orderData['customer_mobile'],
                        'password' => $orderData['customer_mobile'],
                    ]
                );
            }

            return $this->createOrder($user ?? null, $orderData);
        });
    }

    protected function createOrder(?User $user, array $orderData): Order
    {
        $raw = $orderData['code'] ?? null;
        $raw = is_string($raw) ? trim($raw) : $raw;
        $raw = ConvertDatesHelper::convertPersianNumbersToEnglish($raw);

        return $this->orderRepository->create([
            'user_id' => $user ? $user->id : null,
            'seller_id' => auth()->id(),
            'code' => ($raw === null || $raw === '')
                ? CodeGeneratorHelper::generate(get_class(new Order))
                : $raw,
            'description' => $orderData['description'] ?? null,
            'status' => OrderStatus::DRAFT->value,
            'subtotal' => 0,
            'total_price' => 0,
            'type' => $orderData['type'] ?? OrderType::SALE->value,
        ]);
    }

    public function updateOrderPrices(int $orderId)
    {

        $order = $this->orderRepository->find($orderId);
        $order->load('items');
        $subTotal = 0;
        $totalPrice = 0;
        foreach ($order->items as $item) {
            $subTotal += $item->price;
            $totalPrice += $item->total_price;
        }
        $order = $this->orderRepository->update($order, [
            'subtotal' => $subTotal,
            'total_price' => $totalPrice,
        ]);

        return $order;
    }

    public function update(Order $order, array $data): ?Order
    {
        return DB::transaction(function () use ($order, $data) {
            if (! $order->user_id && ! empty($data['customer_mobile'])) {
                $user = resolve(UserService::class)->firstOrCreate(
                    ['mobile' => $data['customer_mobile']],
                    [
                        'name' => $data['customer_name'],
                        'password' => $data['customer_mobile'],
                    ]
                );
                $data['user_id'] = $user->id;
            }

            $order = $this->orderRepository->update($order, $data);

            return $order;
        });
    }

    public function destroy($orderId)
    {
        $order = $this->orderRepository->find($orderId);
        if ($order->status == OrderStatus::DRAFT->value) {
            foreach ($order->items as $item) {
                $this->orderItemService->removeOrderItem($item->id);
            }
            $this->orderRepository->delete($orderId);

            return [
                'status' => true,
            ];
        } else {
            return [
                'status' => false,
                'message' => 'امکان حذف سفارش وجود ندارد',
            ];
        }
    }

    public function confirmFinalApproval(Order $order)
    {
        $order->load('items.product.hall_difinations');

        // Every producible line item must have a defined production route
        // (hall_difinations) before the order can enter the pipeline. Fabric
        // lines (imported as stub products) are record-only and are ignored.
        // Instead of a blanket block, tell the admin exactly which products
        // still need a production route defined.
        $missing = $this->productsMissingProductionRoute($order);
        if ($missing->isNotEmpty()) {
            throw new \RuntimeException(
                __('order::messages.products_without_production_route', ['products' => $missing->pluck('title')->implode('، ')])
            );
        }

        return DB::transaction(function () use ($order) {
            $order = $this->orderRepository->update($order, [
                'status' => OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value,
            ]);
            $this->orderItemService->updateItemsStatus($order, OrderItemStatus::AWAITING_SALES_MANAGER_APPROVAL->value);
            // انتقال مواد اولیه به انبار در حال تولید (پارچه‌ها نادیده گرفته می‌شوند)
            foreach ($order->items as $orderItem) {
                if ($this->isFabricItem($orderItem->product)) {
                    continue;
                }
                // $this->inventoryService->transferToPendingProductionWarehouse($orderItem);
            }

            return $order;
        });
    }

    /**
     * Distinct non-fabric products of an order that have no production route
     * (`hall_difinations`). Used both for the block message and to link the
     * admin to each product's edit page so a route can be defined.
     *
     * @return Collection<int, Product>
     */
    public function productsMissingProductionRoute(Order $order): Collection
    {
        $order->loadMissing('items.product.hall_difinations');

        return $order->items
            ->map->product
            ->filter(fn ($product) => $product
                && ! $this->isFabricItem($product)
                && $product->hall_difinations->isEmpty())
            ->unique('id')
            ->values();
    }

    /**
     * Fabric lines were imported as stub products (unit متر / name «پارچه»,
     * code prefix 103). They are record-only and never enter production.
     */
    protected function isFabricItem($product): bool
    {
        if (! $product) {
            return false;
        }

        return str_contains((string) $product->title, 'پارچه')
            || str_starts_with((string) $product->code, '103');
    }

    public function approveOrder(Order $order): array
    {
        if ($order->status != OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value) {
            return [
                'status' => false,
                'message' => 'سفارش امکان تایید توسط مدیر را ندارد',
            ];
        }

        $order->load('items.product.hall_difinations');

        return DB::transaction(function () use ($order) {
            $this->orderRepository->update($order, ['status' => OrderStatus::APPROVED->value]);
            $this->orderItemService->updateItemsStatus($order, OrderItemStatus::APPROVED->value);

            foreach ($order->items as $orderItem) {
                if (! $orderItem->product) {
                    return [
                        'status' => false,
                        'message' => 'امکان ثبت سفارش یکی از آیتم‌ها (بدون محصول) وجود ندارد.',
                    ];
                }
                // Fabric lines are record-only: skip, they build no production items.
                if ($this->isFabricItem($orderItem->product)) {
                    continue;
                }
                $productHallDifinations = $orderItem->product->hall_difinations;
                if ($productHallDifinations->isEmpty()) {
                    return [
                        'status' => false,
                        'message' => 'برای محصول '.$orderItem->product->title.' هیچ روند تولیدی ثبت نشده است.',
                    ];
                }
                $parentId = null;
                $productHallDifinations->load('hall');
                for ($i = 0; $i < $orderItem->qty; $i++) {
                    // $groupName = CodeGeneratorHelper::generate(
                    //     get_class(new ProductionOrderItem()),
                    //     'group_name',
                    //     $orderItem->id . $i,
                    //     [
                    //         'where' => [
                    //             'order_item_id' => ['!=', $orderItem->id]
                    //         ]
                    //     ]
                    // );
                    $groupName = $order->code.'-'.$orderItem->id.$i;
                    foreach ($productHallDifinations as $productHallDifination) {
                        // if (in_array($productHallDifination->hall->slug, ["painting", "cutting-and-sewing"])) {
                        //     $parentId = null;
                        // }
                        // if ($productHallDifination->hall->slug == "packaging") {
                        //     $sort = 2;
                        // } else {
                        //     $sort = 1;
                        // }

                        // $parent = $this->productionOrderItemRepository->create([
                        //     'order_item_id' => $orderItem->id,
                        //     'qty' => 1,
                        //     'status' => ProductionOrderItemStatus::PENDING->value,
                        //     'product_hall_difination_id' => $productHallDifination->id,
                        //     'parent_id' => $parentId,
                        //     'sort' => $productHallDifination->sort,
                        //     'group_name' => $groupName,
                        // ]);

                        // if ($productHallDifination->hall->slug == "cutting-and-sewing") {
                        //     $parentId = $parent->id;
                        // }
                    }
                }
            }

            return [
                'status' => true,
                'message' => 'سفارش با موفقیت تایید شد و در صف تولید قرار گرفت.',
            ];
        });
    }

    public function canceleOrder(Order $order, string $cancelDescription = '')
    {
        if ($order->status != OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value) {
            return [
                'status' => false,
                'message' => 'سفارش امکان لغو توسط مدیر را ندارد',
            ];
        }

        return DB::transaction(function () use ($cancelDescription, $order) {
            $order->historyDescription = $cancelDescription;

            $order = $this->orderRepository->update($order, [
                'status' => OrderStatus::CANCELED->value,
            ]);
            $this->orderItemService->updateItemsStatus($order, OrderItemStatus::CANCELED->value);
            // بازگرداندن مواد اولیه به انبار
            foreach ($order->items as $orderItem) {
                // $this->inventoryService->restoreToRawMaterialWarehouse($orderItem);
            }

            return [
                'status' => true,
                'message' => 'سفارش با موفقیت لغو شد و مواد اولیه به انبار بازگردانده شد.',
            ];
        });
    }

    public function shipped($orderId)
    {
        $order = $this->orderRepository->find($orderId);

        return DB::transaction(function () use ($order) {
            $order = $this->orderRepository->update($order, ['status' => OrderStatus::SHIPPED->value]);
            $this->orderItemService->updateItemsStatus($order, OrderItemStatus::SHIPPED->value);

            return $order;
        });
    }

    public function delivered($orderId)
    {
        $order = $this->orderRepository->find($orderId);

        return DB::transaction(function () use ($order) {
            $order = $this->orderRepository->update($order, ['status' => OrderStatus::DELIVERED->value]);
            $this->orderItemService->updateItemsStatus($order, OrderItemStatus::DELIVERED->value);

            return $order;
        });
    }

    public function createInternalOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['type'] = OrderType::INTERNAL->value;
            $order = $this->createOrder(null, $data);
            $productsData = [
                'product_id' => $data['product_id'],
                'qty' => $data['qty'],
                // 'status' => OrderItemStatus::AWAITING_SALES_MANAGER_APPROVAL->value,
            ];
            $this->orderItemService->createOrderItem($order, $productsData);
            $this->confirmFinalApproval($order);
            // $order = $this->update($order, ['status' => OrderStatus::AWAITING_SALES_MANAGER_APPROVAL->value]);

            return $order;
        });
    }
}
