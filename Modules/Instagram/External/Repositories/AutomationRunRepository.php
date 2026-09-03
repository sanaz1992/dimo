<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\AutomationRun;
use Modules\Instagram\External\Repositories\Contract\AutomationRunRepositoryInterface;

class AutomationRunRepository extends BaseRepository implements AutomationRunRepositoryInterface
{
    public function __construct(AutomationRun $model)
    {
        parent::__construct($model);
    }
}
