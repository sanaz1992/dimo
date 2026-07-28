<?php

namespace Modules\Transactions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cart\Services\CartManager;
use Modules\Inventory\Actions\CommitReservedInventoryForOrderAction;
use Modules\Inventory\Actions\ReleaseInventoryReservationForOrderAction;
use Modules\Order\Entities\Order;
use Modules\Transactions\Services\PaymentService;

class PaymentCallbackController extends Controller
{
    public function __invoke(
        Request $request,
        Order $order,
        PaymentService $paymentService,
        CartManager $cartManager,
    ) {
        $gateway = $request->string('gateway')->toString();
        DB::beginTransaction();
        try {
            $result = $paymentService->verify(
                order: $order,
                callbackData: $request->all(),
                driver: $gateway ?: null,
            );

            logger()->info('payment callback result', [
                'order_id' => $order->id,
                'gateway' => $gateway,
                'callback_data' => $request->all(),
                'result' => $result,
            ]);
            if ($result->success) {
                $cartManager->clear();
                app(CommitReservedInventoryForOrderAction::class)->execute($order);
                DB::commit();

                return redirect()
                    ->route('cart.index', ['step' => 'payment'])
                    ->with('success', $result->message ?? 'پرداخت با موفقیت انجام شد.')
                    ->with('reference_id', $result->referenceId)
                    ->with('paid_order_id', $order->id);
            }
            $message = $result->message ?? 'پرداخت ناموفق بود.';
        } catch (\Throwable $e) {
            report($e);
            $message = 'در پردازش نتیجه پرداخت خطایی رخ داد.';
        }
        app(ReleaseInventoryReservationForOrderAction::class)->execute($order);
        DB::rollBack();

        return redirect()
            ->route('cart.index', ['step' => 'payment'])
            ->with('error', $message);
    }
}
