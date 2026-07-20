<?php

namespace Modules\User\Http\Livewire\Admin\User;

use Livewire\WithPagination;
use Modules\Core\Http\Livewire\Admin\AdminBaseComponent;
use Modules\User\Enums\UserLevel;
use Modules\User\Services\UserService;

class UserList extends AdminBaseComponent
{
    use WithPagination;
    // use Authorizable;

    public $columns = [];

    public $rows = [];

    public $statusConfig = [];

    public $type;

    public function mount(?string $type = null)
    {
        $this->authorize('users_list');
        $this->type = $type;
        $this->columns = [
            ['key' => 'avatar', 'label' => __('user::attributes.avatar')],
            ['key' => 'name', 'label' => __('user::attributes.name')],
            ['key' => 'mobile', 'label' => __('user::attributes.mobile')],
            ['key' => 'status', 'label' => __('user::attributes.status')],
        ];
    }

    public function render(UserService $userService)
    {
        $conditions = [
            'where' => [
                'level' => ['=', UserLevel::USER->value],
                'id' => ['!=', 1],
            ],
        ];

        $users = $userService->list(null, [10, true], ['mainImageRelation'], $conditions);

        return $this->renderView('User::livewire.admin.user.user-list', compact('users'))
            ->layoutData([
                'title' => __('user::attributes.users_list'),
            ]);
    }
}
