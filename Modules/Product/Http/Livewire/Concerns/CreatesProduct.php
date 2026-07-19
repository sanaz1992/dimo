<?php

namespace Modules\Product\Http\Livewire\Concerns;

use Livewire\WithFileUploads;
use Illuminate\Validation\ValidationException;
use Modules\Category\Services\CategoryService;
use Modules\Core\Traits\LivewireNotify;
use Modules\Product\Enums\ProductExtractionMethod;
use Modules\Product\Enums\ProductGradeEnum;
use Modules\Product\Services\ProductService;
use Illuminate\Validation\Rules\Enum;
use Modules\Core\Helpers\SettingHelper;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Modules\Product\Enums\ProductPackagingType;

trait CreatesProduct
{
    use WithFileUploads;
    use LivewireNotify;

    public $form = [
        'image' => '',
        'name' => '',
        'grade' => '',
        'category_id' => null,
        'description' => '',
        'extraction_method' => '',
        'is_active' => true,
    ];

    public $skuForm = [
        'packaging_type' => '',
        'volume_ml' => '',
        'price' => '',
        'is_active' => ''
    ];


    public $categories;
    public $gardes;
    public $extractionMethods;
    public $imageConfig;
    public string $currentStep = 'basic';
    public array $steps = [
        'basic' => 'اطلاعات پایه',
        'sku' => 'تنوع',
    ];
    public $currency;
    public $initialImage;
    public $product = null;
    public $packagingType;
    protected function loadInitialData($product = null)
    {
        $this->categories = resolve(CategoryService::class)->list(conditions: ['where' => ['is_active' => ['=', true]]]);
        $this->gardes = ProductGradeEnum::labels();
        $this->extractionMethods = ProductExtractionMethod::labels();
        $this->imageConfig = config('media.validations.image');

        $settingHelper = app(SettingHelper::class);
        $this->currency = $settingHelper->currencyLabel();

        if ($product) {
            $product->load('mainImageRelation', 'skus');
            $this->product = $product;
            $this->form['name'] = $product->name;
            $this->form['description'] = $product->description;
            $this->form['category_id'] = $product->category_id;
            $this->form['grade'] = $product->grade;
            $this->form['extraction_method'] = $product->extraction_method;
            $this->form['is_active'] = $product->is_active;

            $this->initialImage = $product->main_image?->getThumbnailUrl('original');

            $this->packagingType = ProductPackagingType::labels();
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
            return [
                'form.image' => ['nullable', 'image', 'max:' . config('media.validations.image.max'), 'mimes:' . config('media.validations.image.mimes')],
                'form.name' => ['required', 'string', 'max:255'],
                'form.grade' => ['nullable', new Enum(ProductGradeEnum::class)],
                'form.category_id' => ['required', 'exists:categories,id'],
                'form.extraction_method' => ['nullable', new Enum(ProductExtractionMethod::class)],
                'form.description' => ['nullable', 'string'],
                'form.is_active' => ['required', 'in:0,1'],
            ];
        }

        if ($this->currentStep === 'sku') {
            return [
                'skuForm.volume_ml' => ['required', 'numeric'],
                'skuForm.packaging_type' => ['required', new Enum(ProductPackagingType::class)],
                'skuForm.price' => ['required', 'numeric'],
                'skuForm.is_active' => ['required', 'in:0,1'],
            ];
        }
        return [];
    }

    protected function validateProduct()
    {
        $this->validate(
            $this->validateCurrentStep(),
            trans('user::validation'),
            trans('user::attributes')
        );
    }

    protected function storeDetail()
    {
        $url = null;
        $message = '';
        try {
            $this->validateProduct();

            if ($this->currentStep === 'basic') {
                if ($this->product) {
                    $product = resolve(ProductService::class)->update($this->product, $this->form);
                    $message = __('core::messages.edit.success');
                } else {
                    $product = resolve(ProductService::class)->create($this->form);
                    $url = redirect()->route('admin.products.edit', ['product' => $product, 'currentStep' => 'sku']);
                    $message = __('core::messages.create.success');
                }
            }
            if ($this->currentStep === 'sku') {
                return [];
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

    public function storeProductSku()
    {
        try {
            $this->validateProduct();
            $productSku = resolve(ProductService::class)->createProductSku($this->product, $this->skuForm);
            $this->product->load('skus');
            $this->notify('success', __('core::messages.create.success'));
        } catch (ValidationException $e) {
            $this->notify('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function deleteProductSku(int $skuId)
    {
        try {
            resolve(ProductService::class)->removeProductSku($this->product, $skuId);
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }

    public function updatedFormImage()
    {
        $this->validate(
            [
                'form.image' => ['image', 'max:' . config('media.validations.image.max'), 'mimes:' . config('media.validations.image.mimes')],
            ],
            trans('product::validation'),
            trans('product::attributes')
        );
    }
    public int $imageUploadKey = 0;
    public function removeImage()
    {
        $this->form['image'] = null;
        $this->imageUploadKey++;
        $this->initialImage = null;
    }

    public function getImagePreviewProperty(): ?string
    {
        $image = $this->form['image'] ?? null;

        if ($image instanceof TemporaryUploadedFile) {
            return $image->temporaryUrl();
        }

        return $this->initialImage;
    }
    public function getClientOriginalNameProperty(): ?string
    {
        $image = $this->form['image'] ?? null;

        if ($image instanceof TemporaryUploadedFile) {
            return $image->getClientOriginalName();
        }

        if ($this->initialImage) {
            return basename(parse_url($this->initialImage, PHP_URL_PATH));
        }

        return null;
    }
}
