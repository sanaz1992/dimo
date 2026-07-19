<?php

namespace Modules\Core\Http\Livewire\Admin;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Modules\Core\Enums\SettingType;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Services\SettingService;
use Modules\Core\Traits\LivewireNotify;

class SettingEdit extends AdminBaseComponent
{
    // use AuthorizesRequests;
    use WithFileUploads;
    use LivewireNotify;

    public array $form = [];
    public $settings;
    public array $initialImage = [];
    public $imageConfig;

    public function mount()
    {
        // $this->authorize('settings_edit');

        $this->imageConfig = config('media.validations.image');

        $settingService = resolve(SettingService::class);
        $this->settings = $settingService->list('created_at:asc', [], ['mainImageRelation']);

        foreach ($this->settings as $setting) {

            // مقدار اولیه text / number / image
            $this->form[$setting->key] =
                $setting->type == SettingType::IMAGE->value
                ? null
                : $setting->value;

            // تصویر اولیه
            $this->initialImage[$setting->key] =
                $setting->medias->isNotEmpty()
                ? $setting->main_image?->getThumbnailUrl('original')
                : null;
        }
    }

    public function updatedFormImage()
    {
        $this->validate(
            [
                'form.image' => [
                    'image',
                    'max:' . config("media.validations.image.max"),
                    'mimes:' . config("media.validations.image.mimes")
                ],
            ],
            trans('product::validation'),
            trans('product::attributes')
        );
    }

    public function removeImage(string $key)
    {

        $deleteMedia =  resolve(SettingService::class)->deleteMedia($key);
        if ($deleteMedia) {
            $this->form[$key] = null;
            $this->initialImage[$key] = null;
            $this->notify('success', __('core::messages.destroy.success'));
        } else {
            $this->notify('error', __('core::messages.destroy.error'));
        }
    }

    public function getImagePreview(string $key)
    {
        return $this->form[$key] instanceof TemporaryUploadedFile
            ? $this->form[$key]->temporaryUrl()
            : $this->initialImage[$key];
    }

    public function getClientOriginalName(string $key)
    {
        if ($this->form[$key] instanceof TemporaryUploadedFile) {
            return $this->form[$key]->getClientOriginalName();
        }

        return $this->initialImage[$key]
            ? basename(parse_url($this->initialImage[$key], PHP_URL_PATH))
            : null;
    }

    public function update()
    {
        resolve(SettingService::class)->update($this->form);
        $this->notify('success', __('core::messages.edit.success'));
    }

    public function render()
    {
        return $this->renderView('Core::livewire.admin.setting-edit')
            ->layoutData([
                'title' => __('core::attributes.settings')
            ]);
    }
}
