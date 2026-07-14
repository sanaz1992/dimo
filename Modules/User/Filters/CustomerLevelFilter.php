<?php

namespace Modules\User\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class CustomerLevelFilter extends QueryFilter
{
    protected array $searchable = ['title'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }
}
