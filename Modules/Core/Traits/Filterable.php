<?php

namespace Modules\Core\Traits;

use Modules\Core\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
