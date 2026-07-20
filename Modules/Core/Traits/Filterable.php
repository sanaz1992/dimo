<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filters\QueryFilter;

trait Filterable
{
    public function scopeFilter(Builder $query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
