<?php

namespace Gp\Support\Models\Traits;

use Gp\Support\Http\Exceptions\InvalidUrlParameterException;
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
        'less_equal' => '<=',
        'like' => 'like'
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
        $table = $model->getTable();
        $filterable = Schema::getColumnListing($model->getTable());

        foreach ($filter as $key => $value) {
            $index = "{$table}.{$key}";
            if (!in_array($key, $filterable)) {
                throw new InvalidUrlParameterException("Invalid Parameter: filter not supported");
            }

            if (is_array($value)) {
                $builder = $this->hasOperator($index, $value, $builder);
                continue;
            }
            $items = explode(',', $value);
            $items = array_map('trim', $items);
            if (count($items) > 1) {
                $builder = $builder->whereIn($index, $items);
                continue;
            }
            $builder = $builder->where($index, $value);
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
            if (!array_key_exists($index, $this->allowedOperators)) {
                throw new InvalidUrlParameterException('Invalid parameter: filter operator');
            }
            if (!is_array($item)) {
                $item = $index === 'like' ? "%{$item}%" : $item;
                $builder = $builder->where($key, $this->allowedOperators[$index], $item);
            }
            continue;
        }

        return $builder;
    }
}
