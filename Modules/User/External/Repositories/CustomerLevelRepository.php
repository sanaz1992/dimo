<?php

namespace Modules\User\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\User\Entities\CustomerLevel;

use Modules\User\External\Repositories\Contract\CustomerLevelRepositoryInterface;

class CustomerLevelRepository extends BaseRepository implements CustomerLevelRepositoryInterface
{
    public function __construct(CustomerLevel $model)
    {
        parent::__construct($model);
    }
}
