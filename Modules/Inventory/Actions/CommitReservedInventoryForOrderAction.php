<?php

namespace Modules\Inventory\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryReservation;
use Modules\Inventory\Enums\InventoryMovementType;
use Modules\Inventory\Enums\InventoryReservationStatus;
use Modules\Order\Entities\Order;
use Modules\Product\Entities\ProductSku;
use RuntimeException;

class CommitReservedInventoryForOrderAction
{
    public function execute(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->stock_deducted_at) {
                return;
            }

            $reservations = InventoryReservation::query()
                ->where('order_id', $order->id)
                ->where('status', InventoryReservationStatus::ACTIVE->value)
                ->lockForUpdate()
                ->get();

            if ($reservations->isEmpty()) {
                throw new RuntimeException('Active inventory reservations not found.');
            }

            foreach ($reservations as $reservation) {
                $sku = ProductSku::query()
                    ->whereKey($reservation->product_sku_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $qty = (int) $reservation->quantity;

                if ((int) $sku->reserved_stock < $qty) {
                    throw new RuntimeException("reserved_stock برای SKU {$sku->id} نامعتبر است.");
                }

                if ((int) $sku->stock < $qty) {
                    throw new RuntimeException("stock واقعی برای SKU {$sku->id} کمتر از reservation است.");
                }

                $sku->decrement('reserved_stock', $qty);
                $sku->decrement('stock', $qty);

                $reservation->update([
                    'status' => InventoryReservationStatus::CONVERTED->value,
                    'converted_at' => now(),
                ]);

                InventoryMovement::query()->create([
                    'product_sku_id' => $sku->id,
                    'order_id' => $reservation->order_id,
                    'order_item_id' => $reservation->order_item_id,
                    'inventory_reservation_id' => $reservation->id,
                    'type' => InventoryMovementType::CONVERT->value,
                    'quantity' => $qty,
                    'meta' => [
                        'message' => 'Reserved inventory converted after successful payment.',
                    ],
                ]);
            }

            // $order->update([
            //     'inventory_status' => 'deducted',
            //     'stock_deducted_at' => now(),
            // ]);
        });
    }
}
