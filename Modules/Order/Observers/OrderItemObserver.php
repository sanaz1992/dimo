<?php

namespace Modules\Order\Observers;

use Modules\Order\Entities\OrderItem;
use Modules\Order\Entities\OrderItemStatusLog;
use Illuminate\Support\Facades\Auth;

class OrderItemObserver
{
    public function updated(OrderItem $orderItem)
    {
        if ($orderItem->isDirty('status')) {
            OrderItemStatusLog::create([
                'order_item_id' => $orderItem->id,
                'status' => $orderItem->status,
                'changed_by' => Auth::id(),
            ]);
        }
    }
}
