<?php

namespace Modules\Core\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $request;

    protected $builder;

    protected array $searchable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                if ($value !== '' && $value !== null) {
                    call_user_func_array([$this, $name], [$value]);
                }
            }
        }

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }

    public function from_date($value)
    {
        return $this->builder->where('created_at', '>=', $value);
    }

    public function to_date($value)
    {
        return $this->builder->where('created_at', '<', $value);
    }

    public function get($key, $default = null)
    {
        return $this->request->query($key, $default);
    }

    public function search($value)
    {
        if (empty($this->searchable)) {
            return $this->builder;
        }

        return $this->builder->where(function ($query) use ($value) {
            foreach ($this->searchable as $index => $column) {
                if ($index === 0) {
                    $query->where($column, 'like', "%{$value}%");
                } else {
                    $query->orWhere($column, 'like', "%{$value}%");
                }
            }
        });
    }
}
