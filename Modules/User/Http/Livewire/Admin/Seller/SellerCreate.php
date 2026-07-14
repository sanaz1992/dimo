<?php

namespace Modules\User\Http\Livewire\Admin\Seller;

use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\Core\Traits\LivewireNotify;
use Modules\User\Entities\City;
use Modules\User\Entities\Province;
use Modules\User\Enums\UserLevel;
use Modules\User\Rules\StoreUserRules;
use Modules\User\Services\UserService;

class SellerCreate extends AdminBaseComponent
{
    use WithFileUploads;
    // use Authorizable;
    use LivewireNotify;
    public $form = [
        'name'     => '',
        'mobile'   => '',
        'password' => '',
        'level' => '',
        'image'    => null,
        'province_id' => null,
        'city_id' => null,
        'address' => '',
        'postal_code' => '',
        'active' => ''
    ];
    public $provinces;
    public $cities = [];

    public function mount()
    {
        // $this->authorize('sellers_create');

        $this->provinces = Province::orderBy('name')->get();
    }
    public function provinceChanged()
    {
        $provinceId = $this->form['province_id'];
        $this->cities = City::where('province_id', $provinceId)->get();
        $this->form['city_id'] = null;
    }

    public function store(UserService $userService)
    {
        try {
            $this->form['level'] = UserLevel::SELLER->value;
            $this->validate(StoreUserRules::rules(), trans('user::validation'), trans('user::attributes'));
            $userService->create($this->form);
            $this->notify('success', __('core::messages.create.success'));

            return redirect()->route('admin.sellers.create');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->notify('error', __('core::messages.create.error'));
        }
    }
    public function render()
    {
        return $this->renderView('User::livewire.admin.seller.seller-create')
            ->layoutData([
                'title' => __('user::attributes.sellers_create')
            ]);
    }
}
