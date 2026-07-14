<?php

namespace Modules\Jetstream\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Jetstream\Entities\Otp;
use Modules\Jetstream\External\Repositories\Contract\OtpRepositoryInterface;

class OtpRepository extends BaseRepository implements OtpRepositoryInterface
{
    public function __construct(Otp $model)
    {
        parent::__construct($model);
    }
}
