<?php

namespace Modules\User\External\Repositories\Contract;

use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;
use Modules\Tenant\Entities\Tenant;

interface TenantRepositoryInterface extends BaseRepositoryInterface
{
    public function restore(string $code): Tenant;
}
