<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\InstagramAccount;
use Modules\Instagram\External\Repositories\Contract\InstagramAccountRepositoryInterface;

class InstagramAccountRepository extends BaseRepository implements InstagramAccountRepositoryInterface
{
    public function __construct(InstagramAccount $model)
    {
        parent::__construct($model);
    }
}
