<?php

namespace Modules\Order\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Services\OrderItemService;
use Modules\Order\Services\OrderService;

class ProductsTable extends Component
{
    use AuthorizesRequests;
    use LivewireNotify;

    public $order;

    public $fabrics = [];

    public $showFabricMeterageInOrder;

    public $currency;

    public $currentUser;

    /** Editable per-item prices, keyed by order item id (draft orders only). */
    public array $prices = [];

    public function mount($order = null)
    {
        $this->currentUser = auth()->user();
        $this->order = $order;
        $this->reloadOrderItems();

        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();
    }

    public function deleteOrderItem($orderItemId)
    {
        try {
            $this->order = resolve(OrderItemService::class)->removeOrderItem($orderItemId);
            $this->reloadOrderItems();
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public function saveItemPrice($orderItemId)
    {
        $raw = ConvertDatesHelper::convertPersianNumbersToEnglish((string) ($this->prices[$orderItemId] ?? ''));
        $raw = str_replace(',', '', $raw);

        try {
            $this->validateOnly("prices.$orderItemId", [
                "prices.$orderItemId" => 'required|numeric|min:0',
            ], [], ["prices.$orderItemId" => __('order::attributes.price')]);

            $orderItem = resolve(OrderItemService::class)->find($orderItemId);
            $price = (int) $raw;
            $total = $price * $orderItem->qty;
            if ($orderItem->custom_frame) {
                $total += $total * 0.10;
            }

            resolve(OrderItemService::class)->update($orderItem, [
                'price' => $price,
                'total_price' => $total,
            ]);
            resolve(OrderService::class)->updateOrderPrices($orderItem->order_id);

            $this->reloadOrderItems();
            $this->notify('success', __('core::messages.edit.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function qualityControle(int $orderItemId, bool $accepted)
    {
        $this->authorize('orders_check_quality');
        try {
            $result = resolve(OrderItemService::class)
                ->checkQuality($orderItemId, $accepted);
            $this->notify('success', $result['message']);
            $this->reloadOrderItems();
        } catch (\Exception $e) {
            $this->notify('error', __('order::messages.order_item_change_status_error'));
        }
    }

    public function packaged(int $orderItemId)
    {
        $this->authorize('orders_packaging');
        try {
            resolve(OrderItemService::class)
                ->packaged($orderItemId);
            $this->notify('success', __('order::messages.order_item_change_status_successfully'));
            $this->reloadOrderItems();
        } catch (\Exception $e) {
            $this->notify('error', __('order::messages.order_item_change_status_error'));
        }
    }

    #[On('order-items-updated')]
    public function reloadOrderItems()
    {
        if ($this->order) {
            $this->order->load(
                'items.product_sku',
            );
            foreach ($this->order->items as $item) {
                $this->prices[$item->id] = $item->price;
            }
        }
    }

    public function render()
    {
        return view('Order::livewire.partials.products-table');
    }
}
