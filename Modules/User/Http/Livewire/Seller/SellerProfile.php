<?php

namespace Modules\User\Http\Livewire\Seller;

// use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Livewire\Seller\SellerBaseComponent;
// use Modules\User\Entities\User;
// use Modules\User\Enums\UserLevel;
// use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
// use Livewire\WithFileUploads;
// use Illuminate\Foundation\Auth\Access\Authorizable;
// use Modules\Core\Traits\LivewireNotify;
// use Modules\User\Rules\UpdateUserProfileRules;
// use Modules\User\Services\UserService;

class SellerProfile extends SellerBaseComponent
{
    // use WithFileUploads;
    // use Authorizable;
    // use LivewireNotify;
    // public User $user;
    // public $form = [
    //     'name'     => '',
    //     'password' => '',
    //     'image'    => null,
    // ];

    // public $message;
    // public $provinces;
    // public $cities = [];
    // public $image;
    // public $initialImage;

    // public function mount()
    // {
    //     $this->user = auth()->user();

    //     if ($this->user->level != UserLevel::SELLER->value) {
    //         return redirect()->route('seller.dashboard')->with('error', 'شما دسترسی لازم را ندارید.');
    //     }

    //     $this->form['name']   = $this->user->name;

    //     $this->initialImage = $this->user->avatar?->getThumbnailUrl('original');
    // }
    // public function updatedFormImage()
    // {
    //     $this->validate([
    //         'form.image' => ['image', 'max:' . config("media.validations.image.max"), 'mimes:' . config("media.validations.image.mimes")]
    //     ]);
    // }

    // public function removeImage()
    // {
    //     $this->form['image'] = null;
    // }

    // public function getImagePreviewProperty()
    // {
    //     if ($this->form['image'] instanceof TemporaryUploadedFile) {
    //         return $this->form['image']->temporaryUrl();
    //     }

    //     return $this->initialImage;
    // }
    // public function getClientOriginalNameProperty()
    // {
    //     return   $this->form['image'] instanceof TemporaryUploadedFile ?
    //         $this->form['image']->getClientOriginalName() :
    //         basename(parse_url($this->initialImage, PHP_URL_PATH));
    // }

    // public function update(UserService $userService)
    // {
    //     try {
    //         $this->validate(UpdateUserProfileRules::rules($this->user->id), trans('user::validation'), trans('user::attributes'));
    //         $userService->update($this->user, $this->form);
    //         $this->notify('success', __('core::messages.edit.success'));
    //     } catch (ValidationException $e) {
    //         throw $e;
    //     } catch (\Exception $e) {
    //         $this->notify('error', __('core::messages.edit.error'));
    //     }
    // }
    // public function render()
    // {
    //     return $this->renderView('User::livewire.seller.seller-profile')
    //         ->layoutData([
    //             'title' => __('user::attributes.user_profile')
    //         ]);
    // }
}
