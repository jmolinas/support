<?php

namespace GP\Support\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait Filterable
{
    protected $allowedOperators = [
        'equal' => '=',
        'not_equal' => '<>',
        'greater_than' => '>',
        'greater_equal' => '>=',
        'less_than' => '<',
        'less_equal' => '<='
    ];

    /**
     * Filterable
     *
     * @param array $filter
     * @param Builder $builder
     * @param array $filterable
     * 
     * @return Builder
     */
    public function filter(array $filter, Builder $builder)
    {
        $model = $builder->getModel();
        $filterable = empty($column = $builder->getQuery()->columns) ?
            Schema::getColumnListing($model->getTable()) : $column;

        foreach ($filter as $key => $value) {
            if (!in_array($key, $filterable)) {
                continue;
            }

            if (is_array($value)) {
                $builder = $this->hasOperator($key, $value, $builder);
                continue;
            }
            $items = explode(',', $value);
            $items = array_map('trim', $items);
            if (count($items) > 1) {
                $builder = $builder->whereIn($key, $items);
                continue;
            }
            $builder = $builder->where($key, $value);
        }
        return $builder;
    }

    /**
     * Has Operator
     *
     * @param [type] $key
     * @param array $value
     * @param Builder $builder
     * 
     * @return null|Builder
     */
    protected function hasOperator($key, array $value, Builder $builder)
    {
        foreach ($value as $index => $item) {
            if (!is_array($item) && array_key_exists($index, $this->allowedOperators)) {
                $builder = $builder->where($key, $this->allowedOperators[$index], $item);
            }
            continue;
        }

        return $builder;
    }
}
