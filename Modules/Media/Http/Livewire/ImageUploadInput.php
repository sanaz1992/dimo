<?php

namespace Modules\Media\Http\Livewire;

use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ImageUploadInput extends Component
{
    use WithFileUploads;

    #[Modelable]
    public $image;

    // public $name = 'image';
    // public $form;
    public $initialImage;

    public $model;

    public function rules()
    {
        return [
            'image' => 'image|max:'.config('media.validations.image.max').'|mimes:'.config('media.validations.image.mimes'),
        ];
    }

    public function mount($initialImage = null)
    {
        // $this->name = $name;
        $this->initialImage = $initialImage;
    }

    public function removeImage()
    {
        $this->image = null;
        $this->initialImage = null;
    }

    public function getImagePreviewProperty()
    {
        if ($this->image instanceof TemporaryUploadedFile) {
            return $this->image->temporaryUrl();
        }

        return $this->initialImage;
    }

    public function getClientOriginalNameProperty()
    {
        return $this->image instanceof TemporaryUploadedFile ?
            $this->image->getClientOriginalName() :
            basename(parse_url($this->initialImage, PHP_URL_PATH));
    }

    public function render()
    {
        return view('media::livewire.image-upload-input');
    }
}
