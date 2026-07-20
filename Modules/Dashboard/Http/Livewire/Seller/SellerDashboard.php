<?php

namespace Modules\Dashboard\Http\Livewire\Seller;

use Modules\Core\Http\Livewire\Seller\SellerBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Enums\UserLevel;

class SellerDashboard extends SellerBaseComponent
{
    use LivewireNotify;

    public $orders;

    public function mount()
    {
        $user = auth()->user();
        if ($user->level != UserLevel::SELLER->value) {
            $this->notify('error', __('core::messages.access_error'));

            return redirect()->route('logout');
        }

    }

    public function render()
    {
        return $this->renderView('dashboard::livewire.seller.seller-dashboard')
            ->layoutData([
                'title' => __('core::attributes.seller_dashboard'),
            ]);
    }
}
