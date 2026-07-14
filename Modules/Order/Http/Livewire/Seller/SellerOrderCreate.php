<?php

namespace Modules\Order\Http\Livewire\Seller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Core\Http\Livewire\Seller\SellerBaseComponent;
use Modules\Order\Http\Livewire\Traits\OrderCreateTrait;
use Modules\User\Enums\UserLevel;

class SellerOrderCreate extends SellerBaseComponent
{
    use AuthorizesRequests;
    use OrderCreateTrait;

    protected $redirectRoute = 'seller.orders.edit';


    public function mount()
    {
        switch (auth()->user()->level) {
            case UserLevel::SELLER->value:
                break;
            default:
                return redirect()->back();
        }
    }
}
