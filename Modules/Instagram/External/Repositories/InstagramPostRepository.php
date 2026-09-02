<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\InstagramPost;
use Modules\Instagram\External\Repositories\Contract\InstagramPostRepositoryInterface;

class InstagramPostRepository extends BaseRepository implements InstagramPostRepositoryInterface
{
    public function __construct(InstagramPost $model)
    {
        parent::__construct($model);
    }
}
