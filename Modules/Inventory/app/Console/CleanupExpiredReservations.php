<?php

namespace Modules\Inventory\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Actions\ReleaseInventoryReservationForOrderAction;
use Modules\Inventory\Entities\InventoryReservation;

class CleanupExpiredReservations extends Command
{
    protected $signature = 'inventory:cleanup-expired';

    protected $description = 'Release expired inventory reservations and update order status';

    public function handle()
    {
        $expiredReservations = InventoryReservation::query()
            ->where('status', 'active')
            ->where('expires_at', '<', now())
            ->with('order')
            ->get();

        if ($expiredReservations->isEmpty()) {
            $this->info('No expired reservations found.');

            return;
        }

        $releaseAction = app(ReleaseInventoryReservationForOrderAction::class);

        foreach ($expiredReservations as $reservation) {
            DB::transaction(function () use ($reservation, $releaseAction) {
                // آزادسازی رزرو و برگشت وضعیت موجودی
                $releaseAction->execute($reservation->order, 'expired');

                // تغییر وضعیت سفارش به منقضی شده
                $reservation->order->update([
                    'status' => 'expired',
                    'payment_status' => 'expired',
                ]);
            });
        }

        $this->info("Successfully released {$expiredReservations->count()} expired reservations.");
    }
}
