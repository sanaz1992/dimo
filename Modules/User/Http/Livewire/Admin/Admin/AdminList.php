<?php

namespace Modules\User\Http\Livewire\Admin\Admin;

use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\User\Enums\UserLevel;
use Modules\User\Services\UserService;

class AdminList extends AdminBaseComponent
{
    use WithPagination;
    // use Authorizable;

    public $columns = [];
    public $rows = [];
    public $statusConfig = [];
    public $type;
    public function mount(string $type = null)
    {
        $this->type = $type;
        // $this->authorize('admins_list');

        $this->columns = [
            ['key' => 'avatar', 'label' =>  __('user::attributes.avatar')],
            ['key' => 'name', 'label' =>  __('user::attributes.name')],
            ['key' => 'mobile', 'label' =>  __('user::attributes.mobile')],
            ['key' => 'status', 'label' =>  __('user::attributes.status')],
        ];
        if ($this->type == "with-expire") {
            $this->columns[] = ['key' => 'expired_at', 'label' =>  __('user::attributes.expired_at')];
        }
        $this->columns[] = ['key' => 'actions', 'label' => ''];
    }

    public function render(UserService $userService)
    {
        $conditions = [
            'where' => [
                'level' => ['=', UserLevel::ADMIN->value],
            ],
        ];
        if ($this->type == "with-expire") {
            $conditions['where']['expired_at'] = ['!=', null];
        }
        $users = $userService->list(null, [10, true], ['mainImageRelation'], $conditions);

        return $this->renderView('User::livewire.admin.admin.admin-list', compact('users'))
            ->layoutData([
                'title' => __('user::attributes.admins_list')
            ]);
    }
}
