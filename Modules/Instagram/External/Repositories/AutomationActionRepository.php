<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\AutomationAction;
use Modules\Instagram\External\Repositories\Contract\AutomationActionRepositoryInterface;

class AutomationActionRepository extends BaseRepository implements AutomationActionRepositoryInterface
{
    public function __construct(AutomationAction $model)
    {
        parent::__construct($model);
    }
}
