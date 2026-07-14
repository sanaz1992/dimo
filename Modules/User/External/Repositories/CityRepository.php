<?php

namespace Modules\User\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\User\Entities\City;
use Modules\User\External\Repositories\Contract\CityRepositoryInterface;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }
}
