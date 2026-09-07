<?php

namespace Modules\Core\External\Repositories;

use Modules\Core\Entities\SyncRun;
use Modules\Core\External\Repositories\Contract\SyncRunRepositoryInterface;

class SyncRunRepository extends BaseRepository implements SyncRunRepositoryInterface
{
    public function __construct(SyncRun $model)
    {
        parent::__construct($model);
    }
}
