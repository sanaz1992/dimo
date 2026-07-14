<?php

namespace Modules\User\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\User\Entities\Province;
use Modules\User\External\Repositories\Contract\ProvinceRepositoryInterface;

class ProvinceRepository extends BaseRepository implements ProvinceRepositoryInterface
{
    public function __construct(Province $model)
    {
        parent::__construct($model);
    }
}
