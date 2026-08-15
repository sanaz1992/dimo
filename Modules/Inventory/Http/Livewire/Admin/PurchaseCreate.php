<?php

namespace Modules\Inventory\Http\Livewire\Admin;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Inventory\Http\Livewire\Concerns\CreatesPurchase;

class PurchaseCreate extends AdminBaseComponent
{
    use CreatesPurchase;

    public function mount()
    {
        $this->loadInitialData();
    }

    public function render()
    {
        return $this->renderView(
            'Inventory::livewire.admin.purchase.purchase-create'
        )->layoutData([
            'title' => __('inventory::attributes.create_purchase'),
        ]);
    }
}
