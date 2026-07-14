<?php

namespace Modules\Product\Http\Livewire\Product;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Modules\Category\Enums\CategoryType;
use Modules\Category\Services\CategoryService;
use Modules\Core\Helpers\SettingHelper;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\Product\Enums\ProductOrderType;
use Modules\Product\Rules\StoreProductRules;
use Modules\Product\Services\ProductService;

class ProductCreate extends AdminBaseComponent
{
    use LivewireNotify;
    use WithFileUploads;

    public $step = 1;

    public $maxStep = 4;

    public $message;

    public $form = [
        'title' => '',
        'code' => '',
        'category_id' => '',
        'description' => '',
        'price' => '',
        'image' => null,
        'gallery' => [],
        'has_fabric' => true,
        'has_frame' => true,
        'order_type' => '',
        'is_intermediate' => '',
    ];

    public $categories = [];

    public $categoryDepth;

    public $parents = [];

    // public $productOrderTypeEnable;
    public $enableProductBaseSales;

    public $enableSelectFrameColor;

    public $autoCalculateProductCost;

    public $currency;

    protected $queryString = [
        'type',
    ];

    public $type;

    public function mount()
    {
        // $this->authorize(['products_create', 'intermediate_products_create']);
        $categoryConditions = [
            'whereNull' => ['parent_id'],
        ];
        if (isset($this->type) && $this->type == 'intermediate') {
            $this->maxStep = 3;
            $categoryConditions = array_merge($categoryConditions, [
                'where' => ['type' => ['=', CategoryType::RAW_MATERIAL->value]],
            ]);
            $this->categoryDepth = 1;
            $this->form['has_fabric'] = false;
            $this->form['has_frame'] = false;
        } else {
            $categoryConditions = array_merge($categoryConditions, [
                'where' => ['type' => ['=', CategoryType::PRODUCT->value]],
            ]);
            $this->categoryDepth = (int) app(SettingHelper::class)->setting('max_product_category_depth')->value;
        }

        $this->categories[0] = app(CategoryService::class)->list(
            orderBy: 'title',
            conditions: $categoryConditions
        );
        // $this->productOrderTypeEnable  = config('product.features.products.order_type');

        $settingHelper = app(SettingHelper::class);
        $this->enableProductBaseSales = $settingHelper->setting('enable_product_base_sales')->value;
        $this->enableSelectFrameColor = $settingHelper->setting('enable_select_frame_color')->value;
        if (! $this->enableSelectFrameColor) {
            $this->form['has_frame'] = false;
        }
        $this->autoCalculateProductCost = $settingHelper->setting('auto_calculate_product_cost')->value;
        $this->currency = $settingHelper->currencyLabel();
    }

    public function updated($name, $value)
    {
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

    public function store(ProductService $productService)
    {
        try {
            if ($this->autoCalculateProductCost) {
                $this->form['price'] = 0;
            }
            // $parentId = $this->parents[$this->categoryDepth - 1];
            $this->form['category_id'] = $this->parents[$this->categoryDepth - 1] ?? null;
            $this->form['is_intermediate'] = $this->type && $this->type == 'intermediate' ? true : false;

            $validationArray = StoreProductRules::rules();
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

            $product = $productService->create($this->form);
            $this->notify('success', __('core::messages.create.success'));

            return redirect()->route('admin.products.edit', ['product' => $product, 'step' => 2]);
        } catch (ValidationException $e) {
            $this->notify('error', $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }

    public $showHasFabric = true;

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

    public function render()
    {
        return $this->renderView('Product::livewire.product.product-create')
            ->layoutData([
                'title' => __('product::attributes.product_create'),
            ]);
    }
}
