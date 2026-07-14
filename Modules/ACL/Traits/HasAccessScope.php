<?php

namespace Modules\ACL\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasAccessScope
{
    public function scopeVisibleFor(Builder $query): Builder
    {
        $user = auth()->user();
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }
        if ($user->hasRole('super_admin')) {
            return $query;
        }
        $user->load('charts');
        if (!count($user->charts)) {
            return $query->whereRaw('1 = 0');
        }
        // مدیر کل → همه چیز
        if ($user->hasChart('company_manager') || $user->hasChart('company_assistance')) {
            return $query;
        }
        $charts = $user->charts;
        // $charts = $charts->whereNull('hall_station_id');

        // // مدل مشخص می‌کند روی چه ستونی فیلتر شود
        // if (!method_exists($this, 'accessColumn')) {
        //     throw new \LogicException(static::class . ' must define accessColumn() method.');
        // }
        $hallCharts = $charts->whereNull('hall_station_id');
        $stationCharts = $charts->whereNotNull('hall_station_id');
        // dd( $hallCharts, $stationCharts);
        $columns = $this->accessColumn();
        // dd($columns);
        if (isset($columns['hall_id'])) {
            // dd($hallIds);
            $hallIds = $hallCharts->pluck('hall_id')->unique()->toArray();
            if (empty($hallIds)) {
                $hallIds = $stationCharts->pluck('hall_id')->unique()->toArray();
            }
            if (!empty($hallIds)) {
                $columns['hall_id'] = explode('.', $columns['hall_id']);
                if (count($columns['hall_id']) > 1) {
                    $query->whereHas($columns['hall_id'][0], function ($r) use ($columns, $hallIds) {
                        if (count($columns['hall_id']) > 2) {
                            $r->whereHas($columns['hall_id'][1], function ($p) use ($columns, $hallIds) {
                                $p->whereIn($columns['hall_id'][2], $hallIds);
                            });
                        } else {
                            $r->whereIn($columns['hall_id'][1], $hallIds);
                        }
                    });
                } else {
                    $query = $query->whereIn($columns['hall_id'][0], $hallIds);
                }
            }
        }

        if (isset($columns['station_id'])) {

            $stationIds = $stationCharts->pluck('hall_station_id')->toArray();

            if (!empty($stationIds)) {
                $columns['station_id'] = explode('.', $columns['station_id']);
                if (count($columns['station_id']) > 1) {
                    $query->whereHas($columns['station_id'][0], function ($r) use ($columns, $stationIds) {
                        $r->whereIn($columns['station_id'][1], $stationIds);
                    });
                } else {
                    $query = $query->whereIn($columns['station_id'][0], $stationIds);
                }
            }
        }
        return $query;
    }
}
