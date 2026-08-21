<?php

namespace Modules\Instagram\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class InstagramAccountFilter extends QueryFilter
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
}
