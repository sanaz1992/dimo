<?php

namespace Modules\User\External\Repositories\Contract;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;
use Modules\User\Entities\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function update(Model $record, array $data): ?Model;

    public function updateCharts(User $user, array $chartsId): void;

    public function restore(string $code): User;
}
