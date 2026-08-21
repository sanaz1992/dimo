<?php

namespace Modules\Instagram\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class InstagramAccountFilter extends QueryFilter
{
    protected array $searchable = ['name'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }

    public function tenant($value)
    {
        return $this->builder->whereHas('tenant', function ($q) use ($value) {
            $q->where('slug', $value);
        });
    }
}
