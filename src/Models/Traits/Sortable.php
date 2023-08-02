<?php

namespace JMolinas\Support\Models\Traits;

use JMolinas\Support\Http\Exceptions\InvalidUrlParameterException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

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
        $columns = Schema::getColumnListing($model->getTable());
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
                throw new InvalidUrlParameterException('Invalid Parameter: not valid sort value');
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
        $model = $builder->getModel();
        $table = $model->getTable();
        $columns = $this->build($builder);
        $rules = $this->sortValidate($sort, $columns);
        foreach ($rules as $key => $value) {
            $index = str_replace('-', '', $key);
            $builder = $builder->orderBy("{$table}.{$index}", $value);
        }
        return $builder;
    }
}
