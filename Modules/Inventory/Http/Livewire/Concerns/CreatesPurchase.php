<?php

namespace Modules\Inventory\Http\Livewire\Concerns;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\WithFileUploads;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Traits\LivewireNotify;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Enums\PurchaseStatus;
use Modules\Inventory\Services\PurchaseItemService;
use Modules\Inventory\Services\PurchaseService;
use Modules\Product\Enums\ProductPackagingType;
use Modules\Product\Services\ProductService;
use Modules\User\Enums\UserLevel;
use Modules\User\Services\UserService;

trait CreatesPurchase
{
    use LivewireNotify;
    use WithFileUploads;

    public $suppliers;

    public $form = [
        'supplier_id' => '',
        'purchased_at' => '',
        'status' => '',
        'invoice_number' => '',
    ];

    public $itemsForm = [
        'product_id' => '',
        'packaging_type' => '',
        'volume_ml' => '',
        'product_sku_id' => '',
        'quantity' => '',
        'purchase_price' => '',
        'sale_price' => '',
    ];

    public string $currentStep = 'basic';

    public array $steps = [
        'basic' => 'اطلاعات پایه',
        'products' => 'محصولات',
    ];

    public $currency;

    public $purchase;

    public $purchaseStatuses;

    public $products;

    public $packagingTypes = [];

    protected function loadInitialData($purchase = null)
    {
        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();

        $this->suppliers = app(UserService::class)->list(
            conditions: [
                'where' => ['level' => ['=', UserLevel::SUPPLIER->value]],
            ]
        );
        $this->purchaseStatuses = PurchaseStatus::labels();
        if ($purchase) {
            $purchase->load('supplier', 'items');
            $this->purchase = $purchase;
            $this->form['supplier_id'] = $purchase->supplier_id;
            $this->form['purchased_at'] = $purchase->purchased_at_jalali;
            $this->form['invoice_number'] = $purchase->invoice_number;
            $this->form['status'] = $purchase->status;
            $this->products = app(ProductService::class)->list();
            $this->packagingTypes = ProductPackagingType::labels();
        }
    }

    public function nextStep(): void
    {
        $this->storeDetail();

        $stepKeys = array_keys($this->steps);

        $currentIndex = array_search(
            $this->currentStep,
            $stepKeys,
            true
        );

        if ($currentIndex === false) {
            return;
        }

        $nextIndex = $currentIndex + 1;

        if (isset($stepKeys[$nextIndex])) {
            $this->currentStep = $stepKeys[$nextIndex];
        }
    }

    public function previousStep(): void
    {
        $stepKeys = array_keys($this->steps);

        $currentIndex = array_search(
            $this->currentStep,
            $stepKeys,
            true
        );

        if ($currentIndex === false) {
            return;
        }

        $nextIndex = $currentIndex - 1;

        if (isset($stepKeys[$nextIndex])) {
            $this->currentStep = $stepKeys[$nextIndex];
        }
    }

    protected function validateCurrentStep(): array
    {
        if ($this->currentStep === 'basic') {
            $this->form['purchased_at'] = ConvertDatesHelper::jalaliToGregorian($this->form['purchased_at']);
            if (! $this->form['invoice_number']) {
                $this->form['invoice_number'] = CodeGeneratorHelper::generate(get_class(new Purchase), 'invoice_number');
            }
            $rules = [
                'form.supplier_id' => ['required', 'exists:users,id'],
                'form.purchased_at' => ['required', 'date'],
                'form.status' => ['required',  new Enum(PurchaseStatus::class)],
                'form.invoice_number' => ['required', 'unique:purchases,invoice_number'],
            ];
            if ($this->purchase) {
                $rules = array_merge($rules, [
                    'form.invoice_number' => ['required', Rule::unique('purchases', 'invoice_number')->ignore($this->purchase->id)],
                ]);
            }

            return $rules;
        }

        if ($this->currentStep === 'products') {
            return [
                'itemsForm.product_id' => ['required', 'exists:products,id'],
                'itemsForm.packaging_type' => ['required', new Enum(ProductPackagingType::class)],
                'itemsForm.volume_ml' => ['required', 'numeric', 'min:100'],
                'itemsForm.quantity' => ['required', 'numeric', 'min:1'],
                'itemsForm.purchase_price' => ['required', 'numeric'],
                'itemsForm.sale_price' => ['required', 'numeric'],
            ];
        }

        return [];
    }

    protected function validatePurchase()
    {
        $this->validate(
            $this->validateCurrentStep(),
            trans('inventory::validation'),
            trans('inventory::attributes')
        );
    }

    protected function storeDetail()
    {
        $url = null;
        $message = '';
        try {

            $this->validatePurchase();

            if ($this->currentStep === 'basic') {
                $purchaseService = resolve(PurchaseService::class);
                if ($this->purchase) {
                    $purchase = $purchaseService->update($this->purchase, $this->form);
                    $message = __('core::messages.edit.success');
                } else {
                    $purchase = $purchaseService->create($this->form);
                    $url = redirect()->route('admin.purchases.edit', ['purchase' => $purchase, 'currentStep' => 'products']);
                    $message = __('core::messages.create.success');
                }
            }

        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }

        if ($message) {
            $this->notify('success', $message);
        }
        if ($url) {
            return $url;
        }
    }

    public function storeProduct()
    {
        try {
            $this->validatePurchase();
            if ($this->currentStep === 'products') {
                $purchaseItem = app(PurchaseItemService::class)->create($this->purchase, $this->itemsForm);
                $this->purchase->load('items.product_sku');
            }
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
        $this->notify('success', __('inventory::messages.add_product.success'));
    }

    public function deleteItem($purchaseItemId)
    {
        try {
            if ($this->purchase->status != PurchaseStatus::DRAFT->value) {
                $this->notify('error', __('inventory::messages.product_received_and_you_cant_remove_it'));

                return 0;
            }
            app(PurchaseItemService::class)->delete($purchaseItemId);
            $this->notify('success', __('inventory::messages.delete_product.success'));

            $this->purchase->load('items.product_sku');
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public $showEditItemModal = false;

    public $selectedPurchaseItem;

    public $editItemForm = [
        'quantity' => '',
        'purchase_price' => '',
        'sale_price' => '',
    ];

    public function editItem($purchaseItemId)
    {
        $this->showEditItemModal = true;
        $this->selectedPurchaseItem = app(PurchaseItemService::class)->find($purchaseItemId);
        $this->selectedPurchaseItem->load('product_sku.product');
        $this->editItemForm = [
            'quantity' => $this->selectedPurchaseItem->quantity,
            'purchase_price' => $this->selectedPurchaseItem->purchase_price,
            'sale_price' => $this->selectedPurchaseItem->sale_price,
        ];
    }

    public function updateItem()
    {
        try {
            $this->selectedPurchaseItem = app(PurchaseItemService::class)->update($this->selectedPurchaseItem, $this->editItemForm);
            $this->purchase->load('items.product_sku');
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
        $this->notify('success', __('inventory::messages.update_product.success'));
        $this->showEditItemModal = false;
    }
}
