<?php

namespace Modules\User\Http\Livewire\Admin\Seller;

use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Entities\City;
use Modules\User\Entities\Province;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;
use Modules\User\Rules\UpdateUserRules;
use Modules\User\Services\UserService;

class SellerEdit extends AdminBaseComponent
{
    use WithFileUploads;
    // use Authorizable;
    use LivewireNotify;
    public User $user;
    public $form = [
        'name'     => '',
        'mobile'   => '',
        'password' => '',
        'level'    => '',
        'image'    => null,
        'province_id' => null,
        'city_id' => null,
        'address' => '',
        'postal_code' => '',
        'active' => ''
    ];

    public $message;
    public $provinces;
    public $cities = [];
    public $image;
    public $initialImage;

    public function mount(User $user)
    {
        // $this->authorize('sellers_edit');
        if ($user->level != UserLevel::SELLER->value) {
            return redirect()->route('admin.sellers.index')->with('error', 'شما دسترسی لازم را ندارید.');
        }

        $this->user           = $user;
        $this->form['name']   = $user->name;
        $this->form['mobile'] = $user->mobile;
        $this->form['level']  = $user->level;
        $this->form['active'] = (bool) $user->active;


        $address =  $user->addresses()->first();
        $this->form['province_id'] = $address->city->province_id;
        $this->form['city_id'] = $address->city_id;
        $this->form['address'] = $address->address;
        $this->form['postal_code'] = $address->postal_code;

        $this->initialImage = $user->avatar?->getThumbnailUrl('original');

        $this->provinces = Province::orderBy('name')->get();
        $this->cities = City::where('province_id', $address->city->province_id)->orderBy('name')->get();
    }
    public function updatedFormImage()
    {
        $this->validate([
            'form.image' => ['image', 'max:' . config("media.validations.image.max"), 'mimes:' . config("media.validations.image.mimes")]
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
    public function getClientOriginalNameProperty()
    {
        return   $this->form['image'] instanceof TemporaryUploadedFile ?
            $this->form['image']->getClientOriginalName() :
            basename(parse_url($this->initialImage, PHP_URL_PATH));
    }

    public function provinceChanged()
    {
        $provinceId = $this->form['province_id'];
        $this->cities = City::where('province_id', $provinceId)->get();
        $this->form['city_id'] = null;
    }

    public function update(UserService $userService)
    {
        try {
            $this->validate(UpdateUserRules::rules($this->user->id), trans('user::validation'), trans('user::attributes'));
            $userService->update($this->user, $this->form);
            $this->notify('success', __('core::messages.edit.success'));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.edit.error'));
        }
    }
    public function render()
    {
        return $this->renderView('User::livewire.admin.seller.seller-edit')
            ->layoutData([
                'title' => __('user::attributes.sellers_edit')
            ]);
    }
}
