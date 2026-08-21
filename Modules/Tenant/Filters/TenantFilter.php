<?php

namespace Modules\Tenant\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class TenantFilter extends QueryFilter
{
    protected array $searchable = ['name', 'timezone', 'local'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }

    public function status($value)
    {
        return $this->builder->where('status', $value);
    }

    public function user($value)
    {
        return $this->builder->whereHas('users', function ($q) use ($value) {
            $q->where('unique_code', $value);
        });
    }
}
