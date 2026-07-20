<?php

namespace Modules\Order\Filters;

use Illuminate\Http\Request;
use Modules\Core\Filters\QueryFilter;

class OrderFilter extends QueryFilter
{
    protected array $searchable = ['description'];

    public function __construct(
        Request $request,
    ) {
        parent::__construct($request);
    }

    public function code($value)
    {
        return $this->builder->where('code', 'LIKE', '%'.$value.'%');
    }

    public function seller($value)
    {
        return $this->builder->whereHas('seller', function ($q) use ($value) {
            $q->where('name', 'LIKE', '%'.$value.'%')
                ->orWhere('unique_code', 'LIKE', '%'.$value.'%')
                ->orWhere('mobile', 'LIKE', '%'.$value.'%');
        });
    }

    public function user($value)
    {
        return $this->builder->whereHas('user', function ($q) use ($value) {
            $q->where('id', $value);
        });
    }

    public function created_at_from($value)
    {
        return $this->builder->where('created_at', '>=', $value);
    }

    public function created_at_to($value)
    {
        return $this->builder->where('created_at', '<=', $value);
    }

    public function started_at_from($value)
    {
        return $this->builder->whereHas('items.production_order_items', function ($q) use ($value) {
            $q->where('started_at', '>=', $value);
        });
    }

    public function started_at_to($value)
    {
        return $this->builder->whereHas('items.production_order_items', function ($q) use ($value) {
            $q->where('started_at', '<=', $value);
        });
    }

    public function finished_at_from($value)
    {
        return $this->builder->whereHas('items.production_order_items', function ($q) use ($value) {
            $q->where('finished_at', '>=', $value);
        });
    }

    public function finished_at_to($value)
    {
        return $this->builder->whereHas('items.production_order_items', function ($q) use ($value) {
            $q->where('finished_at', '<=', $value);
        });
    }

    public function price_min($value)
    {
        return $this->builder->where('total_price', '>=', $value);
    }

    public function price_max($value)
    {
        return $this->builder->where('total_price', '<=', $value);
    }

    public function hall($value)
    {
        return $this->builder->whereHas('items.production_order_items.product_hall_difination.hall', function ($q) use ($value) {
            $q->where('slug', $value);
        });
    }
}
