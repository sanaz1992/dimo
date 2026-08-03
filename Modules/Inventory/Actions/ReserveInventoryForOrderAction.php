<?php

namespace Modules\Inventory\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryReservation;
use Modules\Inventory\Enums\InventoryReservationStatus;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Product\Entities\ProductSku;
use RuntimeException;

class ReserveInventoryForOrderAction
{
    public function execute(Order $order, int $ttlMinutes = 15): void
    {
        DB::transaction(function () use ($order, $ttlMinutes) {
            $order = Order::query()
                ->with('items')
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingActiveReservations = InventoryReservation::query()
                ->where('order_id', $order->id)
                ->where('status', InventoryReservationStatus::ACTIVE->value)
                ->exists();

            if ($existingActiveReservations) {
                return;
            }

            foreach ($order->items as $item) {
                $sku = ProductSku::query()
                    ->whereKey($item->product_sku_id)
                    ->lockForUpdate()
                    ->first();

                if (! $sku) {
                    throw new RuntimeException("SKU برای آیتم {$item->id} یافت نشد.");
                }

                $qty = (int) $item->quantity;
                $availableStock = (int) $sku->stock - (int) $sku->reserved_stock;

                if ($availableStock < $qty) {
                    throw new RuntimeException("موجودی SKU {$sku->id} کافی نیست.");
                }

                $reservation = InventoryReservation::query()->create([
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'product_sku_id' => $sku->id,
                    'quantity' => $qty,
                    'status' => InventoryReservationStatus::ACTIVE->value,
                    'expires_at' => now()->addMinutes($ttlMinutes),
                ]);

                $sku->increment('reserved_stock', $qty);

                InventoryMovement::query()->create([
                    'product_sku_id' => $sku->id,
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'inventory_reservation_id' => $reservation->id,
                    'type' => 'reserve',
                    'quantity' => $qty,
                    'meta' => [
                        'message' => 'Inventory reserved before payment.',
                    ],
                ]);
            }

            $order->update([
                // 'inventory_status' => 'reserved',
                'status' => OrderStatus::AWAITING_PAYMENT->value,
                'payment_status' => OrderStatus::AWAITING_PAYMENT->value,
                // 'reserved_at' => now(),
                // 'payment_expires_at' => now()->addMinutes($ttlMinutes),
            ]);
        });
    }
}
