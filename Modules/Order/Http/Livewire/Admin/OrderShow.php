<?php

namespace Modules\Order\Http\Livewire\Admin;

use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Services\NoteService;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Services\OrderProductionService;
use Modules\Order\Services\OrderService;

class OrderShow extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use LivewireNotify;

    public $order;

    public $totalSteps;

    public $doneSteps;

    public $currency;

    public function mount(Order $order)
    {
        // $this->authorize('orders_show');
        $order->load('shipment_items');
        $this->order = $order;
        $this->reloadOrderItems();
        $order->load('user');
        [$this->totalSteps, $this->doneSteps] = resolve(OrderProductionService::class)
            ->calculateProductionProgress($order);

        $this->currency = app(SettingHelper::class)->currencyLabel();
    }

    public function reloadOrderItems()
    {
        $this->order->load(
            'notes.creator',
            'items.product',
            'items.item_fabrics.fabric',
            'items.item_fabrics.color',
            'items.item_fabrics.product_required_fabric',
            'items.status_logs'
        );
        if ($this->order->status == OrderStatus::CANCELED->value) {
            $this->order->load(['histories' => function ($q) {
                $q->where('new_value', OrderStatus::CANCELED->value);
            }]);
        }
    }

    public function approveOrder()
    {
        $this->authorize('orders_approved');
        try {
            $result = resolve(OrderService::class)->approveOrder($this->order);
            if ($result['status']) {
                $this->notify('success', __('order::messages.confirm_order_successfully'));
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

    public function shipped(int $orderId)
    {
        $this->authorize('orders_shipped');
        try {
            $this->order = resolve(OrderService::class)
                ->shipped($orderId);
            $this->notify('success', __('order::messages.order_change_status_successfully'));
        } catch (\Exception $e) {
            $this->notify('error', __('order::messages.order_item_change_status_error'));
        }
    }

    public function delivered(int $orderId)
    {
        $this->authorize('orders_shipped');
        try {
            $this->order = resolve(OrderService::class)
                ->delivered($orderId);
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
        resolve(NoteService::class)->create($this->order, ['value' => $this->note]);
        $this->order->load('notes.creator');
        $this->note = '';
    }

    public function render()
    {
        return $this->renderView('Order::livewire.order.admin.order-show')
            ->layoutData([
                'title' => __('order::attributes.orders_show'),
            ]);
    }
}
