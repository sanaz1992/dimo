<?php

namespace Modules\Inventory\Http\Livewire\Admin;

use Livewire\WithPagination;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Inventory\Services\PurchaseService;

class PurchaseList extends AdminBaseComponent
{
    use WithPagination;

    // use Authorizable;
    public $sortOptions = [];

    public $currency;

    public function mount()
    {
        $this->authorize('purchases_list');

        $this->currency = app(SettingHelper::class)->currencyLabel();

        $this->sortOptions = [
            'created_at:desc' => __('core::attributes.newest'),
            'created_at:asc' => __('core::attributes.oldest'),

        ];
    }

    public function render(PurchaseService $purchaseService)
    {
        $purchases = $purchaseService->list(null, [10, true], ['supplier', 'items']);

        return $this->renderView('Inventory::livewire.admin.purchase.purchase-list', compact('purchases'))
            ->layoutData([
                'title' => __('inventory::attributes.purchases_list'),
            ]);
    }
}
