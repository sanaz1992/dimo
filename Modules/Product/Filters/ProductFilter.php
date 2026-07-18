<?php

namespace Modules\Product\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class ProductFilter extends QueryFilter
{
    protected array $searchable = ['description', 'title', 'code'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }

    public function category($value)
    {
        return $this->builder->whereHas('category', function ($q) use ($value) {
            $q->where('slug', $value);
        });
    }


    public function published($value)
    {
        if ($value === '1') {
            return $this->builder->where('is_publish', true);
        }

        if ($value === '0') {
            return $this->builder->where('is_publish', false);
        }
    }
}
