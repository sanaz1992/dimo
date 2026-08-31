<?php

namespace Modules\Instagram\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class ConversationFilter extends QueryFilter
{
    protected array $searchable = [];

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
