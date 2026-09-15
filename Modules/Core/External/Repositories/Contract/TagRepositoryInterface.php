<?php

namespace Modules\Core\External\Repositories\Contract;

use Modules\Core\Entities\Tag;

interface TagRepositoryInterface extends BaseRepositoryInterface
{
    public function restore(string $code): Tag;
}
