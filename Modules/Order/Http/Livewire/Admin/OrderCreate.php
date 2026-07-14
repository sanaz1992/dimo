<?php

namespace Modules\Order\Http\Livewire\Admin;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Order\Http\Livewire\Traits\OrderCreateTrait;
use Modules\User\Enums\UserLevel;

class OrderCreate extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use OrderCreateTrait;

    protected $redirectRoute = 'admin.orders.edit';

    public function mount()
    {
        switch (auth()->user()->level) {
            case UserLevel::ADMIN->value:
                // $this->authorize('orders_create');
                break;
            default:
                return redirect()->back();
        }
    }
}
