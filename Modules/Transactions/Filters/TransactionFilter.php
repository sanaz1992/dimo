<?php

namespace Modules\Transactions\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class TransactionFilter extends QueryFilter
{
    protected array $searchable = [];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }
}
