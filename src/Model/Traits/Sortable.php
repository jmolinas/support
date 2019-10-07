<?php

namespace GP\Support\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    private $rule = [
        'asc',
        'desc'
    ];

    /**
     * Build Columns
     *
     * @param Builder $builder
     * 
     * @return array
     */
    protected function build(Builder $builder)
    {
        $model = $builder->getModel();
        $columns = empty($column = $builder->getQuery()->columns) ?
            Schema::getColumnListing($model->getTable()) : $column;
        $rules = [];

        foreach ($this->rule as $value) {
            array_map(function ($item) use (&$rules, $value) {
                $key = $value === 'desc' ? "-{$item}" : $item;
                $rules[$key] = $value;
            }, $columns);
        }
        return $rules;
    }

    /**
     * Validate Sort
     * 
     * @param array $rule
     * @param mixed $sort
     * 
     * @return array
     */
    protected function sortValidate($sort, array $rule)
    {
        $sort = is_array($sort) ? $sort : explode(',', $sort);
        $columns = [];
        foreach ($sort as $value) {
            if (array_key_exists($value, $rule) === false) {
                continue;
            }
            $columns[$value] = $rule[$value];
        }
        return $columns;
    }

    /**
     * Apply Order By
     *
     * @param Builder $builder
     * 
     * @return Builder
     */
    public function order(Builder $builder, $sort)
    {
        $columns = $this->build($builder);
        $rules = $this->sortValidate($sort, $columns);
        foreach ($rules as $key => $value) {
            $index = str_replace('-', '', $key);
            $builder = $builder->orderBy($index, $value);
        }
        return $builder;
    }
}
