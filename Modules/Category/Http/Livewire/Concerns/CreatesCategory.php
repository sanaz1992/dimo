<?php

namespace Modules\Category\Http\Livewire\Concerns;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Category\Services\CategoryService;
use Modules\Core\Traits\LivewireNotify;

trait CreatesCategory
{
    use LivewireNotify;
    use WithFileUploads;

    public $form = [
        'image' => '',
        'name' => '',
        'description' => '',
        'is_active' => true,
    ];

    public $imageConfig;

    public $category = null;

    public $initialImage;

    protected function loadInitialData($category = null)
    {
        $this->imageConfig = config('media.validations.image');

        if ($category) {
            $category->load('mainImageRelation');
            $this->category = $category;
            $this->form['name'] = $category->name;
            $this->form['is_active'] = $category->is_active;
            $this->form['description'] = $category->description;

            $this->initialImage = $category->main_image?->getThumbnailUrl('original');
        }
    }

    protected function validateCategory()
    {
        $this->validate(
            [
                'form.image' => ['nullable', 'image', 'max:'.config('media.validations.image.max'), 'mimes:'.config('media.validations.image.mimes')],
                'form.name' => ['required', 'string', 'max:255'],
                'form.is_active' => ['required', 'in:0,1'],
            ],
            trans('category::validation'),
            trans('category::attributes')
        );
    }

    public function store()
    {
        $message = '';
        try {
            $this->validateCategory();

            if ($this->category) {
                $category = resolve(CategoryService::class)->update($this->category, $this->form);
                $message = __('core::messages.edit.success');
            } else {
                $category = resolve(CategoryService::class)->create($this->form);
                $this->reset('form');
                $message = __('core::messages.create.success');
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->notify('error', __('core::messages.edit.error'));
        }

        if ($message) {
            $this->notify('success', $message);
        }
    }

    public function updatedFormImage()
    {
        $this->validate(
            [
                'form.image' => ['image', 'max:'.config('media.validations.image.max'), 'mimes:'.config('media.validations.image.mimes')],
            ],
            trans('category::validation'),
            trans('category::attributes')
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
