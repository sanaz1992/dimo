<?php

namespace Modules\Core\Filters;

use Illuminate\Http\Request;

class TagFilter extends QueryFilter
{
    protected array $searchable = ['name'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }

    public function user($value)
    {
        return $this->builder->whereHas('tenant.users', function ($q) use ($value) {
            $q->where('unique_code', $value);
        });
    }
}
