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

    public function account($value)
    {
        return $this->builder->whereHas('instagramAccount', function ($q) use ($value) {
            $q->where('unique_code', $value);
        });
    }

    public function tenants($value)
    {
        return $this->builder->whereHas('instagramAccount', function ($q) use ($value) {
            $q->whereIn('tenant_id', $value);
        });
    }
}
