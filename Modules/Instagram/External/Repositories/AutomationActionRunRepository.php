<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\AutomationActionRun;
use Modules\Instagram\External\Repositories\Contract\AutomationActionRunRepositoryInterface;

class AutomationActionRunRepository extends BaseRepository implements AutomationActionRunRepositoryInterface
{
    public function __construct(AutomationActionRun $model)
    {
        parent::__construct($model);
    }
}
