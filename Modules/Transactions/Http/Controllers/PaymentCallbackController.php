<?php

namespace Modules\Transactions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cart\Services\CartManager;
use Modules\Inventory\Actions\CommitReservedInventoryForOrderAction;
use Modules\Inventory\Actions\ReleaseInventoryReservationForOrderAction;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Services\OrderService;
use Modules\Transactions\Enums\TransactionStatus;
use Modules\Transactions\Services\PaymentService;
use Modules\Transactions\Services\TransactionService;

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

        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
        } catch (\Throwable $e) {
            report($e);
            $message = 'در پردازش نتیجه پرداخت خطایی رخ داد.';
        }
        DB::rollBack();

        app(ReleaseInventoryReservationForOrderAction::class)->execute($order);
        app(OrderService::class)->update($order, ['payment_status' => OrderStatus::PAYMENT_FAILED->value]);
        $transaction = app(TransactionService::class)->findByColumn('order_id', $order->id);
        app(TransactionService::class)->update($transaction, ['status' => TransactionStatus::FAILED->value]);

        return redirect()
            ->route('cart.index', ['step' => 'payment'])
            ->with('error', $message);
    }
}
