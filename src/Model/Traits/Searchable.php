<?php

namespace GP\Support\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Search Builder
     *
     * @param array $search
     * @param Builder $builder
     * @param array $searchable
     * 
     * @return Builder
     */
    public function search(array $search, Builder $builder)
    {
        $counter = 1;
        $model = $builder->getModel();
        $searchable = empty($column = $builder->getQuery()->columns) ?
            Schema::getColumnListing($model->getTable()) : $column;

        foreach ($search as $key => $value) {
            if (!in_array($key, $searchable)) {
                continue;
            }
            if ($counter === 1) {
                $builder = $builder->where($key, 'like', "%{$value}%");
                continue;
            }
            $builder = $builder->orWhere($key, 'like', "%{$value}%");
        }
        return $builder;
    }
}
