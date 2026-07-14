<?php

namespace Modules\Order\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Services\OrderItemService;
use Modules\Order\Services\OrderService;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;

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

    /** Sellers (نماینده فروش) as [id => "name — mobile"] for the assignment select. */
    public array $sellers = [];

    /** Currently selected نماینده for this order. */
    public $sellerId = null;

    public function mount($order = null)
    {
        $this->currentUser = auth()->user();
        $this->order = $order;
        $this->reloadOrderItems();

        $this->sellerId = $this->order?->seller_id;
        $this->sellers = User::where('level', UserLevel::SELLER->value)
            ->orderBy('name')
            ->get(['id', 'name', 'mobile'])
            ->mapWithKeys(fn ($u) => [$u->id => trim($u->name.' — '.$u->mobile)])
            ->toArray();

        $settingHelper = app(SettingHelper::class);
        $this->showFabricMeterageInOrder = $settingHelper->setting('show_fabric_meterage_in_order')->value;

        $this->currency = app(SettingHelper::class)->currencyLabel();
    }

    /** Assign / reassign the نماینده فروش (seller) of this order. */
    public function saveSeller()
    {
        $this->authorize('orders_edit');

        if (! $this->order) {
            return;
        }

        // The seller is locked once the order leaves draft (it is in production).
        if ($this->order->status !== OrderStatus::DRAFT->value) {
            $this->notify('error', __('core::messages.edit.error'));

            return;
        }

        try {
            $this->validate([
                'sellerId' => ['required', Rule::exists('users', 'id')->where('level', UserLevel::SELLER->value)],
            ], [], ['sellerId' => __('order::attributes.seller')]);

            $this->order = resolve(OrderService::class)->update($this->order, ['seller_id' => $this->sellerId]);
            $this->reloadOrderItems();
            $this->notify('success', __('core::messages.edit.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
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
                'items.product',
                'items.item_fabrics.fabric',
                'items.item_fabrics.color',
                'items.item_fabrics.product_required_fabric'
            );
            $this->fabrics = [];
            foreach ($this->order->items as $item) {
                $this->prices[$item->id] = $item->price;
                foreach ($item->item_fabrics as $itemFabric) {
                    $this->fabrics[$itemFabric->fabric_id] = [
                        'code' => $itemFabric->fabric->code,
                        'title' => $itemFabric->fabric->fabric_model?->title,
                        'color_code' => $itemFabric->fabric->color_code,
                        'qty' => ($this->fabrics[$itemFabric->fabric_id]['qty'] ?? 0) + $itemFabric->qty,
                        'price' => $itemFabric->fabric_price,
                    ];
                }
            }
        }
    }

    public function render()
    {
        return view('Order::livewire.order.products-table');
    }
}
