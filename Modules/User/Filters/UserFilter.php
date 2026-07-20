<?php

namespace Modules\User\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class UserFilter extends QueryFilter
{
    protected array $searchable = ['unique_code'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }

    public function name($value)
    {
        return $this->builder->where('name', 'LIKE', '%'.$value.'%');
    }

    public function mobile($value)
    {
        return $this->builder->where('mobile', 'LIKE', '%'.$value.'%');
    }

    public function level($value)
    {
        return $this->builder->where('level', $value);
    }

    public function active($value)
    {
        return $this->builder->where('active', $value);
    }
}
