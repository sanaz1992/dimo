<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Services\CartManager;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Order\Enums\OrderItemStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Exceptions\OutOfStockException;
use Modules\Order\External\Contracts\OrderRepositoryInterface;
use Modules\User\Services\UserService;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderItemService $orderItemService,
        protected CartManager $cartManager
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

    public function createFromCart(Cart $cart, array $data)
    {
        $data['status'] = 'pending';

        return DB::transaction(function () use ($cart, $data) {
            $order = $this->create($data);

            // create order items
            $cart->load('items');
            foreach ($cart->items as $item) {
                $orderItem = $this->orderItemService->createOrderItem($order, $item);
                // error if it doesnt have enough stock
                if (! $orderItem instanceof OrderItem) {
                    throw new OutOfStockException(
                        __('messages.out_of_stock_item', ['product' => $item->product->name]),
                        $item
                    );
                }
            }
            // update order prices
            $this->updateOrderPrices($order->id);

            return $order;
        });
    }

    public function create(array $orderData): Order
    {
        $raw = $orderData['order_number'] ?? null;
        $raw = is_string($raw) ? trim($raw) : $raw;
        $raw = ConvertDatesHelper::convertPersianNumbersToEnglish($raw);
        $orderData['order_number'] = ($raw === null || $raw === '')
            ? CodeGeneratorHelper::generate(get_class(new Order), 'order_number')
            : $raw;

        return DB::transaction(function () use ($orderData) {
            return $this->orderRepository->create($orderData);
        });
    }

    public function updateOrderPrices(int $orderId)
    {
        $order = $this->orderRepository->find($orderId);
        $order->load('items');
        $subTotal = 0;
        $totalPrice = 0;
        $discount = 0;
        foreach ($order->items as $item) {
            $subTotal += $item->price * $item->quantity;
            $discount += $item->discount * $item->quantity;
            $totalPrice += $subTotal - $discount;
        }
        $order = $this->orderRepository->update($order, [
            'subtotal' => $subTotal,
            'total_amount' => $totalPrice,
            'discount_amount' => $discount,
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
                    $groupName = $order->order_number.'-'.$orderItem->id.$i;
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
}
