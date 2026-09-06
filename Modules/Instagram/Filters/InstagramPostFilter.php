<?php

namespace Modules\Instagram\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class InstagramPostFilter extends QueryFilter
{
    protected array $searchable = ['name'];

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function account($value)
    {
        return $this->builder->whereHas('instagramAccount', function ($q) use ($value) {
            $q->where('unique_code', $value);
        });
    }
}
