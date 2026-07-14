<?php

namespace Modules\Order\Http\Livewire\Seller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Http\Livewire\Seller\SellerBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Order\Entities\Order;
use Modules\Order\Http\Livewire\Traits\OrderEditTrait;
use Modules\Order\Services\OrderItemService;
use Modules\Order\Services\OrderService;
use Modules\Category\Services\CategoryService;
use Modules\User\Enums\UserLevel;

class SellerOrderEdit extends SellerBaseComponent
{
    use AuthorizesRequests;
    use OrderEditTrait;

    public function mount(Order $order)
    {
        $authUser = auth()->user();
        switch ($authUser->level) {
            case UserLevel::SELLER->value:
                if ($order->seller_id != $authUser->id) {
                    $this->notify('error', __('core::messages.access_error'));
                    return redirect()->route('seller.dashboard');
                }
                break;
            default:
                return redirect()->back();
        }
        $this->order = $order;

        if ($order->user_id) {
            $order->load('user');
            $this->form['customer_name'] = $order->user->name;
            $this->form['customer_mobile'] = $order->user->mobile;
        }
        $this->form['description'] = $order->description;
    }
}
