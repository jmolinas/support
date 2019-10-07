<?php
namespace GP\Support\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Page
{
    protected $allowParam = [
        'offset',
        'limit'
    ];

    /**
     * Page Filters
     *
     * @param array $filter
     * @param Builder $builder
     * 
     * @return Builder
     */
    public function page(array $filter, Builder $builder)
    {
        foreach ($filter as $key => $value) {
            if (! in_array($key, $this->allowParam)) {
                continue;
            }
            $builder = $builder->{$key}($value);
        }
        return $builder;
    }
}
