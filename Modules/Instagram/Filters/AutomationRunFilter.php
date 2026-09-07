<?php

namespace Modules\Instagram\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class AutomationRunFilter extends QueryFilter
{
    protected array $searchable = ['name'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }

    public function automationRule($value)
    {
        return $this->builder->where('automation_rule_id', $value);
    }
}
