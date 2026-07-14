<?php

namespace Modules\User\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\User\Entities\Address;
use Modules\User\External\Repositories\Contract\AddressRepositoryInterface;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }
}
