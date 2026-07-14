<?php

namespace Modules\Product\Http\Livewire\Product;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Factory\Services\HallService;
use Modules\Product\Entities\Product;
use Modules\Product\Enums\ProductOrderType;
use Modules\Product\Rules\UpdateProductRules;
use Modules\Product\Services\CostItemService;
use Modules\Product\Services\ProductService;
use Modules\Warehouse\Enums\Warehouses;
use Modules\Warehouse\External\Repositories\Contract\RawMaterialWarehouseRepositoryInterface;
use Modules\Warehouse\Services\WarehouseService;

class ProductEdit extends AdminBaseComponent
{
    use LivewireNotify;
    use WithFileUploads;

    public $product;

    public $step = 1;

    public $maxStep = 4;

    public $message;

    public $image;

    public $initialImage;

    public $form = [
        'title' => '',
        'code' => '',
        'price' => '',
        'category_id' => '',
        'description' => '',
        'image' => null,
        'has_fabric' => '',
        'has_frame' => '',
        'order_type' => '',
        'cost_items' => [],
    ];

    public $categories = [];

    public $categoryDepth;

    public $parents = [];

    public $intermediateProducts;

    public $rawMaterials;

    public $rawMaterialUnit = null;

    public $selectedMaterial;

    public $halls;

    protected $queryString = ['step', 'type'];

    public $hallId;

    public $sort;

    public $productPrice;

    public $enableProductBaseSales;

    public $enableSelectFrameColor;

    public $showHasFabric = true;

    public $currency;

    public $autoCalculateProductCost;

    public $costItems;

    public $type;

    public function mount(Product $product)
    {
        // $this->authorize( 'products_edit');

        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();
        $this->autoCalculateProductCost = $settingHelper->setting('auto_calculate_product_cost')?->value;

        $product->load('mainImageRelation');
        $this->product = $product;
        $this->reloadProduct();
        $this->form['title'] = $product->title;
        $this->form['code'] = $product->code;
        $this->form['price'] = $product->price;
        // $this->form['category_id'] = $product->category_id;
        $this->form['description'] = $product->description;
        $this->form['has_fabric'] = (bool) $product->has_fabric;
        $this->form['has_frame'] = (bool) $product->has_frame;
        $this->form['order_type'] = $product->order_type;
        // $this->categories = app(CategoryService::class)->list(
        //     orderBy: 'title',
        //     conditions: [
        //         'where' => ['type' => ['=', CategoryType::PRODUCT->value]]
        //     ]
        // );

        if ($this->product->is_intermediate) {
            $this->type = 'intermediate';
        }
        if (isset($this->type) && $this->type == 'intermediate') {
            $this->maxStep = 3;
            $categoryConditions = [
                'where' => ['type' => ['=', CategoryType::RAW_MATERIAL->value]],
            ];
            $this->categoryDepth = 1;
        } else {
            $categoryConditions = [
                'where' => ['type' => ['=', CategoryType::PRODUCT->value]],
            ];
            $this->categoryDepth = (int) (app(SettingHelper::class)->setting('max_product_category_depth')?->value ?? 1);
        }

        $currnetCategory = $this->product->category;
        for ($i = $this->categoryDepth - 1; $i >= 0; $i--) {
            $this->categories[$i] = resolve(CategoryService::class)->list(
                conditions: [
                    'where' => array_merge(
                        $categoryConditions['where'],
                        ['parent_id' => ['=', $currnetCategory->parent_id]]
                    ),
                ]
            );
            $this->parents[$i] = $currnetCategory->id;
            if ($currnetCategory->parent_id == null) {
                break;
            }
            $currnetCategory = $currnetCategory->parent;
        }

        // If the stored category is shallower than categoryDepth (e.g. a root category
        // was saved when depth=2), the loop broke early and data is at wrong indices.
        // Shift everything down to start from index 0.
        if ($i > 0) {
            for ($j = $i; $j < $this->categoryDepth; $j++) {
                $this->categories[$j - $i] = $this->categories[$j];
                $this->parents[$j - $i] = $this->parents[$j];
                unset($this->categories[$j], $this->parents[$j]);
            }
            // Load child categories for levels beyond what was selected
            $numSelected = $this->categoryDepth - $i;
            for ($j = $numSelected; $j < $this->categoryDepth; $j++) {
                $parentId = $this->parents[$j - 1] ?? null;
                $this->categories[$j] = $parentId
                    ? resolve(CategoryService::class)->list(conditions: [
                        'where' => ['parent_id' => ['=', $parentId]],
                    ])
                    : [];
            }
        }

        $this->initialImage = $product->main_image?->getThumbnailUrl('original');

        $this->rawMaterials = resolve(WarehouseService::class)->list(
            Warehouses::RAW_MATERIALS_WAREHOUSE->value,
            'title:asc'
        );

        $this->intermediateProducts = resolve(ProductService::class)->list(
            'title:asc',
            conditions: [
                'where' => [
                    'is_intermediate' => ['=', true],
                    'is_publish' => ['=', true],
                ],
                'whereNotIn' => ['id' => [[$this->product->id]]],
            ]
        );

        $this->halls = resolve(HallService::class)->list(null, [], ['stations']);

        $this->enableProductBaseSales = $settingHelper->setting('enable_product_base_sales')->value;
        $this->enableSelectFrameColor = $settingHelper->setting('enable_select_frame_color')->value;
        if (! $this->enableSelectFrameColor) {
            $this->form['has_frame'] = false;
        }

        if ($product->order_type == ProductOrderType::BASE->value) {
            $this->showHasFabric = false;
            $this->form['has_fabric'] = false;
        } else {
            $this->showHasFabric = true;
            $this->form['has_fabric'] = $product->has_fabric;
        }

        $this->costItems = resolve(CostItemService::class)->list(conditions: ['where' => ['is_active' => ['=', true]]]);
        $this->product->load('cost_items');
        foreach ($this->product->cost_items as $ci) {
            $this->form['cost_items'][$ci->id] = $ci->pivot->amount;
        }

        // Calculate totals AFTER all data (materials, cost items) is loaded
        $this->updateMaterialTotalPrice();
        $this->updateIntermediateProductMaterialTotalPrice();
        $this->updateCostItemsTotalPrice();
        $this->updateGrandTotalPrice();
    }

    public function reloadProduct()
    {
        $this->product->load(['materials', 'intermediate_products', 'hall_difinations' => function ($q) {
            $q->orderBy('sort', 'asc');
        }, 'hall_difinations.hall', 'required_fabrics']);
    }

    public function updatedFormImage()
    {
        $this->validate(
            [
                'form.image' => ['image', 'max:'.config('media.validations.image.max'), 'mimes:'.config('media.validations.image.mimes')],
            ],
            trans('product::validation'),
            trans('product::attributes')
        );
    }

    public function removeImage()
    {
        $this->form['image'] = null;
    }

    public function getImagePreviewProperty()
    {
        if ($this->form['image'] instanceof TemporaryUploadedFile) {
            return $this->form['image']->temporaryUrl();
        }

        return $this->initialImage;
    }

    public function getClientOriginalNameProperty()
    {
        return $this->form['image'] instanceof TemporaryUploadedFile ?
            $this->form['image']->getClientOriginalName() :
            basename(parse_url($this->initialImage, PHP_URL_PATH));
    }

    public function next()
    {
        switch ($this->step) {
            case 1:
                $this->actionStep1();
                break;
            case 2:
            case 3:
                $this->step++;
                break;
            case 4:
                // dd($this->productPrice);
                // $this->actionStep4();
                break;
        }
    }

    public function prev(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public $rawMaterialForm = [
        'id' => '',
        'qty' => '',
        'title' => '',
        'code' => '',
        'price' => '',
    ];

    public $intermediateProductForm = [
        'id' => '',
        'qty' => '',
        'title' => '',
        'code' => '',
        'price' => '',
    ];

    public $materialTotalPrice;

    public $intermediateProductmaterialTotalPrice;

    public $costItemsTotalPrice;

    public $grandTotalPrice;

    public function updatedRawMaterialFormId($value)
    {
        $this->selectedMaterial = resolve(RawMaterialWarehouseRepositoryInterface::class)->find($value);
        if ($this->selectedMaterial->unit) {
            $this->rawMaterialUnit = $this->selectedMaterial->unit;
        } else {
            $this->rawMaterialUnit = '';
        }
        reset($this->rawMaterialForm);
    }

    public function addRawMaterialRow()
    {
        try {
            $this->validate(
                [
                    'rawMaterialForm.id' => 'required|exists:raw_materials_warehouse,id',
                    'rawMaterialForm.qty' => 'required|numeric|min:0.1',
                ],
                trans('product::validation'),
                trans('product::attributes')
            );
            resolve(ProductService::class)->addMaterial($this->product, [
                'id' => $this->rawMaterialForm['id'],
                'qty' => $this->rawMaterialForm['qty'],
            ]);
            $this->reloadProduct();
            $this->updateMaterialTotalPrice();
            $this->notify('success', __('core::messages.create.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            $this->notify('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    public function removeProductMaterialRow($materialId)
    {
        try {
            resolve(ProductService::class)->deleteMaterial($this->product, $materialId);
            $this->reloadProduct();
            $this->updateMaterialTotalPrice();
            $this->notify('success', __('core::messages.destroy.success'));
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public function updateMaterialTotalPrice()
    {
        $this->materialTotalPrice = 0;
        foreach ($this->product->materials as $material) {
            $this->materialTotalPrice += $material->price * $material->pivot->qty;
        }
        $this->updateGrandTotalPrice();
    }

    public function addIntermediateProductRow()
    {
        try {
            $this->validate(
                [
                    'intermediateProductForm.id' => 'required|exists:products,id',
                    'intermediateProductForm.qty' => 'required|numeric|min:0.1',
                ],
                trans('product::validation'),
                trans('product::attributes')
            );
            resolve(ProductService::class)->addIntermediateProductToMaterials($this->product, [
                'id' => $this->intermediateProductForm['id'],
                'qty' => $this->intermediateProductForm['qty'],
            ]);
            $this->reloadProduct();
            $this->updateIntermediateProductMaterialTotalPrice();
            $this->notify('success', __('core::messages.create.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            $this->notify('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    public function removeIntermediateProductMaterialRow($intermediateProductId)
    {
        try {
            resolve(ProductService::class)->deleteIntermediateProductMaterial($this->product, $intermediateProductId);
            $this->reloadProduct();
            $this->updateIntermediateProductMaterialTotalPrice();
            $this->notify('success', __('core::messages.destroy.success'));
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public function updateIntermediateProductMaterialTotalPrice()
    {
        $this->intermediateProductmaterialTotalPrice = 0;
        foreach ($this->product->intermediate_products as $product) {
            $this->intermediateProductmaterialTotalPrice += $product->final_price * $product->pivot->qty;
        }
        $this->updateGrandTotalPrice();
    }

    public function updateCostItemsTotalPrice()
    {
        $this->costItemsTotalPrice = 0;
        foreach ($this->product->cost_items as $ci) {
            $this->costItemsTotalPrice += $ci->base_price * $ci->pivot->amount;
        }
        $this->updateGrandTotalPrice();
    }

    public function updateGrandTotalPrice()
    {
        if ($this->autoCalculateProductCost) {
            // Auto-calculation on: derive cost from the bill of materials.
            $this->grandTotalPrice = ($this->materialTotalPrice ?? 0)
                + ($this->intermediateProductmaterialTotalPrice ?? 0)
                + ($this->costItemsTotalPrice ?? 0);
        } else {
            // Auto-calculation off: the cost is simply the manually entered fixed price.
            $this->grandTotalPrice = (int) ($this->product->price ?? 0);
        }
    }

    public function storeStation()
    {
        try {
            $this->validate(
                [
                    'hallId' => ['required', 'exists:halls,id'],
                    'sort' => ['nullable', 'numeric', 'min:1'],
                ],
                trans('product::validation'),
                trans('product::attributes')
            );
            resolve(ProductService::class)->createProductHallDefine($this->product, $this->hallId, $this->sort);
            $this->notify('success', __('core::messages.create.success'));
            $this->reloadProduct();
        } catch (ValidationException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            $this->notify('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    public function removeStation($hallId)
    {
        try {
            resolve(ProductService::class)->removeProductHallDefine($this->product, $hallId);
            $this->notify('success', __('core::messages.destroy.success'));
            $this->reloadProduct();
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public $fabricForm = [
        'title' => '',
        'qty' => '',
    ];

    public function addFabricRow()
    {
        try {
            $this->validate(
                [
                    'fabricForm.title' => 'required|string',
                    'fabricForm.qty' => 'required|numeric:strict|min:0.1',
                ],
                trans('product::validation'),
                trans('product::attributes')
            );
            resolve(ProductService::class)->addFabric($this->product, $this->fabricForm);
            $this->reloadProduct();
            $this->notify('success', __('core::messages.create.success'));
            reset($this->fabricForm);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    public function removeFabricRow($productFabricId)
    {
        try {
            resolve(ProductService::class)->deleteProductFabric($productFabricId);
            $this->notify('success', __('core::messages.destroy.success'));
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public function changeOrderType($value)
    {
        if ($value == ProductOrderType::BASE->value) {
            $this->showHasFabric = false;
            $this->form['has_fabric'] = false;
        } else {
            $this->showHasFabric = true;
            $this->form['has_fabric'] = true;
        }
    }

    public function updated($name, $value)
    {
        if (str_starts_with($name, 'form.cost_items.')) {
            $this->updateCostItemsTotalPrice();
        }

        if (str_starts_with($name, 'parents.')) {

            $level = (int) str_replace('parents.', '', $name);

            $typeCondition = (isset($this->type) && $this->type === 'intermediate')
                ? ['type' => ['=', CategoryType::RAW_MATERIAL->value]]
                : ['type' => ['=', CategoryType::PRODUCT->value]];

            $this->categories[$level + 1] = resolve(CategoryService::class)->list(conditions: [
                'where' => array_merge($typeCondition, ['parent_id' => ['=', $value]]),
            ]);

            unset($this->parents[$level + 1]);
        }
    }

    public function actionStep1()
    {
        try {
            if ($this->autoCalculateProductCost) {
                $this->form['price'] = 0;
            }
            $this->form['category_id'] = $this->parents[$this->categoryDepth - 1] ?? null;
            $this->form['is_intermediate'] = $this->type && $this->type == 'intermediate' ? true : false;

            $validationArray = UpdateProductRules::rules($this->product->id);
            if ($this->enableProductBaseSales) {
                $validationArray = array_merge($validationArray, [
                    'form.order_type' => ['required', 'string', Rule::enum(ProductOrderType::class)],
                ]);
            }
            $this->validate(
                $validationArray,
                trans('product::validation'),
                trans('product::attributes')
            );

            resolve(ProductService::class)->update($this->product, $this->form);
            $this->product->load('mainImageRelation');
            $this->initialImage = $this->product->main_image?->getThumbnailUrl('original');
            $this->form['image'] = null;
            $this->notify('success', __('core::messages.edit.success'));
            $this->step++;
        } catch (ValidationException $e) {
            $this->notify('error', $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function publishProduct()
    {
        $this->authorize('products_publish');
        foreach ($this->form['cost_items'] as $key => $value) {
            $this->form['cost_items'][$key] = ConvertDatesHelper::convertPersianNumbersToEnglish($value);
        }
        try {
            $this->validate(
                [
                    'form.cost_items' => 'nullable|array',
                    'form.cost_items.*' => 'required|numeric:strict|min:0.0',
                ],
                trans('product::validation'),
                trans('product::attributes')
            );
            resolve(ProductService::class)->updateCostItems($this->product, $this->form['cost_items']);
            resolve(ProductService::class)->publishProduct($this->product->id, true);
            $this->product->load('cost_items');
            $this->updateCostItemsTotalPrice();
            $this->notify('success', __('product::messages.change_publish_status.success'));
        } catch (InvalidArgumentException $e) {
            $this->notify('error', $e->getMessage());
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->notify('error', __('product::messages.change_publish_status.error'));
        }
    }

    public function render()
    {
        return $this->renderView('Product::livewire.product.product-edit')
            ->layoutData([
                'title' => __('product::attributes.product_edit'),
            ]);
    }
}
