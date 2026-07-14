<?php

namespace Modules\User\Http\Livewire\Admin\Concerns;

use Livewire\WithFileUploads;
use Modules\User\Rules\StoreUserRules;
use Modules\User\Services\UserService;

trait CreatesUser
{
    use WithFileUploads;
    public $form = [
        'name'     => '',
        'mobile'   => '',
        'password' => '',
        'image'    => null,
        'active'   => true,
    ];

    protected function userRules(): array
    {
        return StoreUserRules::rules();
    }

    protected function validateUser()
    {
        $this->validate(
            $this->userRules(),
            trans('user::validation'),
            trans('user::attributes')
        );
    }

    protected function createUser(UserService $userService, string $level)
    {
        $this->form['level'] = $level;
        return $userService->create($this->form);
    }

    public function updatedFormImage()
    {
        $this->validate([
            'form.image' => [
                'image',
                'max:' . config("media.validations.image.max"),
                'mimes:' . config("media.validations.image.mimes")
            ]
        ]);
    }

    public function removeImage()
    {
        $this->form['image'] = null;
    }
}
