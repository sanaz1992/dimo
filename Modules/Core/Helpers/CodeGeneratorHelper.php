<?php

namespace Modules\Core\Helpers;

use Illuminate\Database\Eloquent\SoftDeletes;

class CodeGeneratorHelper
{
    public static function generate(string $modelClass, string $column = 'code', string $prefix = '', array $conditions = [])
    {
        while (true) {
            $code = rand(1000, 100000);
            if ($prefix != '') {
                $code = $prefix.'-'.$code;
            }

            if (in_array(SoftDeletes::class, class_uses_recursive($modelClass))) {
                // model has SoftDelete
                $query = $modelClass::withTrashed();
            } else {
                // model without SoftDelete
                $query = $modelClass::query();
            }
            foreach ($conditions as $key => $condition) {
                if ($key == 'where') {
                    foreach ($condition as $col => $val) {
                        if ($val instanceof \Closure) {
                            $query = $query->where($val);
                            continue;
                        }
                        if ($val[0] == 'LIKE') {
                            $val[1] = '%'.$val[1].'%';
                        }
                        $query = $query->where($col, $val[0], $val[1]);
                    }
                }
            }
            $found = $query->where($column, $code)->first();
            if (!$found) {
                break;
            }
        }

        return $code;
    }
}
