<?php

namespace Modules\User\Http\Livewire\Admin\Concerns;

use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Modules\Core\Helpers\SettingHelper;
use Modules\User\Entities\User;
use Modules\User\Rules\UpdateUserRules;
use Modules\User\Services\UserService;

trait EditsUser
{
    use WithFileUploads;

    public User $user;

    public $form = [
        'name'     => '',
        'mobile'   => '',
        'password' => '',
        'level'    => '',
        'image'    => null,
        'active'   => '',
    ];

    public $initialImage;
    public $showAdminPasswordMessage;

    protected function fillUserForm(User $user): void
    {
        $this->user = $user;

        $this->form['name']   = $user->name;
        $this->form['mobile'] = $user->mobile;
        $this->form['level']  = $user->level;
        $this->form['active'] = (bool) $user->active;
        $settingHelper = app(SettingHelper::class);
        $this->showAdminPasswordMessage =
            $settingHelper->setting('user_can_register')->value == '1'
            && auth()->id() == $user->id
            && Hash::check($user->mobile, $user->password)
            ? true : false;

        $this->initialImage = $user->avatar?->getThumbnailUrl('original');
    }

    public function getClientOriginalNameProperty()
    {
        return   $this->form['image'] instanceof TemporaryUploadedFile ?
            $this->form['image']->getClientOriginalName() :
            basename(parse_url($this->initialImage, PHP_URL_PATH));
    }
    public function updatedFormImage()
    {
        $this->validate([
            'form.image' => [
                'image',
                'max:' . config('media.validations.image.max'),
                'mimes:' . config('media.validations.image.mimes'),
            ],
        ]);
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

    protected function userRules(): array
    {
        return UpdateUserRules::rules($this->user->id);
    }

    protected function validateUser()
    {
        $this->validate(
            $this->userRules(),
            trans('user::validation'),
            trans('user::attributes')
        );
    }
    public function updateUser(UserService $userService)
    {
        return $userService->update($this->user, $this->form);
    }
}
