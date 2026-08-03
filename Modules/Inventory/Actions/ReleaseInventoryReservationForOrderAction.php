<?php

namespace Modules\Inventory\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\InventoryMovement;
use Modules\Inventory\Entities\InventoryReservation;
use Modules\Inventory\Enums\InventoryMovementType;
use Modules\Inventory\Enums\InventoryReservationStatus;
use Modules\Order\Entities\Order;
use Modules\Product\Entities\ProductSku;

class ReleaseInventoryReservationForOrderAction
{
    public function execute(Order $order, string $reason = 'released'): void
    {
        DB::transaction(function () use ($order, $reason) {
            $order = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $reservations = InventoryReservation::query()
                ->where('order_id', $order->id)
                ->where('status', InventoryReservationStatus::ACTIVE->value)
                ->lockForUpdate()
                ->get();

            if ($reservations->isEmpty()) {
                return;
            }

            foreach ($reservations as $reservation) {
                $sku = ProductSku::query()
                    ->whereKey($reservation->product_sku_id)
                    ->lockForUpdate()
                    ->first();

                if ($sku) {
                    $qty = (int) $reservation->quantity;

                    if ((int) $sku->reserved_stock >= $qty) {
                        $sku->decrement('reserved_stock', $qty);
                    }
                }

                $reservation->update([
                    'status' => $reason === InventoryReservationStatus::EXPIRED->value ?? InventoryReservationStatus::RELEASED->value,
                    'released_at' => now(),
                    'expired_at' => $reason === 'expired' ? now() : null,
                ]);

                InventoryMovement::query()->create([
                    'product_sku_id' => $reservation->product_sku_id,
                    'order_id' => $reservation->order_id,
                    'order_item_id' => $reservation->order_item_id,
                    'inventory_reservation_id' => $reservation->id,
                    'type' => InventoryMovementType::RELEASE->value,
                    'quantity' => $reservation->quantity,
                    'meta' => [
                        'reason' => $reason,
                    ],
                ]);
            }

            // $order->update([
            //     'inventory_status' => 'released',
            //     'released_at' => now(),
            // ]);
        });
    }
}
