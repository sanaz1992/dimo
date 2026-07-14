<?php

namespace Modules\Order\Http\Livewire\Traits;

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Services\OrderService;
use Modules\User\Enums\UserLevel;

trait OrderEditTrait
{
    use LivewireNotify;

    public $order;

    public $product;

    public $count = 0;

    public $form = [];

    /** Products missing a production route: [['title' => , 'url' => ], ...] — shown as links after a failed final approval. */
    public array $missingRouteProducts = [];

    public function submitOrder()
    {
        $this->dispatch('request-customer-data');

        // store description for order becuse description is out of product selector
        resolve(OrderService::class)->update($this->order, ['description' => $this->form['description']]);
    }

    #[On('customer-data-submitted')]
    public function handleCustomerData(array $data)
    {
        $data['description'] = $this->form['description'] ?? null;
        $redirect = false;
        try {
            if ($this->order->code != $data['code']) {
                $redirect = true;
            }
            resolve(OrderService::class)->update($this->order, $data);
            $this->notify('success', __('core::messages.edit.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
        if ($redirect) {
            if (auth()->user()->level == UserLevel::ADMIN->value) {
                return redirect()->route('admin.orders.edit', $this->order);
            } elseif (auth()->user()->level == UserLevel::SELLER->value) {
                return redirect()->route('seller.orders.edit', $this->order);
            }

            return redirect()->route('login');
        }
    }

    public function confirmFinalApproval()
    {
        $this->missingRouteProducts = [];

        try {
            resolve(OrderService::class)->confirmFinalApproval($this->order);
            $this->notify('success', __('order::messages.confirm_order_successfully'));

            // The order has left draft and entered production, so send the user
            // to the read-only order page instead of the (now gutted) edit form.
            if (auth()->user()->level == UserLevel::SELLER->value) {
                return redirect()->route('seller.orders.show', $this->order);
            }

            return redirect()->route('admin.orders.show', $this->order);
        } catch (\Exception $e) {
            report($e);

            // The service set the status to "awaiting approval" on the in-memory
            // order before the transaction rolled back; reload it so the edit
            // page (which only renders while the order is a draft) does not
            // collapse after a failed approval.
            $this->order->refresh();

            // If the block was caused by products without a production route,
            // surface them as links to their edit page so the admin can act.
            $missing = resolve(OrderService::class)->productsMissingProductionRoute($this->order);
            if ($missing->isNotEmpty()) {
                $canEdit = auth()->user()?->can('products_edit');
                $this->missingRouteProducts = $missing->map(fn ($product) => [
                    'title' => $product->title,
                    'url' => $canEdit ? route('admin.products.edit', $product) : null,
                ])->all();
            }

            $this->notify('error', empty($e->getMessage()) ?
                __('order::messages.error_in_confirm_order')
                : $e->getMessage()
            );
        }
    }

    public function render()
    {
        return $this->renderView('Order::livewire.order.order-edit')
            ->layoutData([
                'title' => __('order::attributes.edit_order'),
            ]);
    }
}
