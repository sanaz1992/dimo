<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\InstagramComment;
use Modules\Instagram\External\Repositories\Contract\InstagramCommentRepositoryInterface;

class InstagramCommentRepository extends BaseRepository implements InstagramCommentRepositoryInterface
{
    public function __construct(InstagramComment $model)
    {
        parent::__construct($model);
    }
}
