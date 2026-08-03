<?php

namespace Modules\Order\Http\Livewire\Admin;

use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Services\OrderService;

class OrderShow extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use LivewireNotify;

    public $order;

    public $currency;

    public function mount(Order $order)
    {
        // $this->authorize('orders_show');
        $this->order = $order;
        $this->reloadOrderItems();
        $order->load('user');

        $this->currency = app(SettingHelper::class)->currencyLabel();
    }

    public function reloadOrderItems()
    {
        $this->order->load(
            'items.product_sku.product',
        );
    }

    public function approveOrder()
    {
        $this->authorize('orders_approved');
        try {
            $result = resolve(OrderService::class)->approveOrder($this->order);
            if ($result['status']) {
                $this->notify('success', $result['message']);
            } else {
                $this->notify('error', $result['message']);
            }
        } catch (\Exception $e) {
            $this->notify('error', __('order::messages.approved.error'));
        }
    }

    public function doneProccessing()
    {
        if ($this->order->status != OrderStatus::PROCESSING) {
            $this->notify('error', 'وضعیت سفارش برای این تغییر معتبر نیست');

            return;
        }
        $this->authorize('orders_approved');
        try {
            $result = resolve(OrderService::class)->changeStatus($this->order, OrderStatus::AWAITING_SHIPPED->value);
            if ($result['status']) {
                $this->notify('success', $result['message']);
            } else {
                $this->notify('error', $result['message']);
            }
        } catch (\Exception $e) {
            $this->notify('error', __('order::messages.approved.error'));
        }
    }

    public $showCancelOrderModal = false;

    public $cancel_description;

    public function openCancelOrderModal()
    {
        $this->showCancelOrderModal = true;
    }

    public function cancelOrder()
    {
        if (! $this->cancel_description) {
            $this->notify('error', __('order::messages.description_for_cancel_order_is_required'));
        }
        try {
            $this->authorize('orders_approved');
            $result = resolve(OrderService::class)->canceleOrder($this->order, $this->cancel_description);
            if ($result['status']) {
                $this->order->refresh();
                $this->reloadOrderItems();
                $this->notify('success', __('order::messages.cancel_order_successfully'));
            } else {
                $this->notify('error', $result['message']);
            }
            $this->showCancelOrderModal = false;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public function shipped()
    {
        $this->authorize('orders_shipped');
        try {
            $this->order = resolve(OrderService::class)
                ->shipped($this->order->id);
            $this->notify('success', __('order::messages.order_change_status_successfully'));
        } catch (\Exception $e) {
            $this->notify('error', __('order::messages.order_item_change_status_error'));
        }
    }

    public function delivered()
    {
        $this->authorize('orders_shipped');
        try {
            $this->order = resolve(OrderService::class)
                ->delivered($this->order->id);
            $this->notify('success', __('order::messages.order_change_status_successfully'));
        } catch (\Exception $e) {
            $this->notify('error', __('order::messages.order_item_change_status_error'));
        }
    }

    public $showAddNoteModal = false;

    public $note;

    public function addNoteModal()
    {
        $this->showAddNoteModal = true;
    }

    public function storeNote()
    {
        $this->showAddNoteModal = false;
        // resolve(NoteService::class)->create($this->order, ['value' => $this->note]);
        // $this->order->load('notes.creator');
        $this->note = '';
    }

    public function render()
    {
        return $this->renderView('Order::livewire.admin.order.order-show')
            ->layoutData([
                'title' => __('order::attributes.orders_show').' '.$this->order->order_number,
            ]);
    }
}
