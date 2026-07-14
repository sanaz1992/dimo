<?php

namespace Modules\Order\Http\Livewire\Admin;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Order\Entities\Order;
use Modules\Order\Http\Livewire\Traits\OrderEditTrait;
use Modules\User\Enums\UserLevel;

class OrderEdit extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use OrderEditTrait;

    public function mount(Order $order)
    {
        $authUser = auth()->user();
        switch ($authUser->level) {
            case UserLevel::ADMIN->value:
                // $this->authorize('orders_create');
                break;
            default:
                return redirect()->back();
        }
        $this->order = $order;
        $this->form['code'] = $this->order->code;
        if ($order->user_id) {
            $order->load('user');
            $this->form['customer_name'] = $order->user->name;
            $this->form['customer_mobile'] = $order->user->mobile;
        }
        $this->form['description'] = $order->description;
    }
}
