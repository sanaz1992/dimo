<?php

namespace Modules\Inventory\Http\Livewire\Admin;

use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Http\Livewire\Concerns\CreatesPurchase;

class PurchaseEdit extends AdminBaseComponent
{
    use CreatesPurchase;

    protected $queryString = ['currentStep'];

    public function mount(Purchase $purchase)
    {
        // $this->authorize( 'purchases_edit');
        $this->loadInitialData($purchase);
    }

    public function render()
    {
        return $this->renderView('Inventory::livewire.admin.purchase.purchase-edit')
            ->layoutData([
                'title' => __('inventory::attributes.edit_purchase'),
            ]);
    }
}
