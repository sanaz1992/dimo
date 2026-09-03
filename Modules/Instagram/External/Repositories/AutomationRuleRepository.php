<?php

namespace Modules\Instagram\External\Repositories;

use Modules\Core\External\Repositories\BaseRepository;
use Modules\Instagram\Entities\AutomationRule;
use Modules\Instagram\External\Repositories\Contract\AutomationRuleRepositoryInterface;

class AutomationRuleRepository extends BaseRepository implements AutomationRuleRepositoryInterface
{
    public function __construct(AutomationRule $model)
    {
        parent::__construct($model);
    }
}
