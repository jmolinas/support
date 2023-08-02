<?php
namespace JMolinas\Support\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use JMolinas\Support\Http\Exceptions\InvalidUrlParameterException;

trait Row
{
    protected $allowParam = [
        'offset',
        'limit',
        'number',
        'size'
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
                throw new InvalidUrlParameterException("Invalid Parameter: page value");
            }
            if (filter_var($value, FILTER_VALIDATE_INT) === false && (int) $value < 1) {
                throw new InvalidUrlParameterException("Invalid Parameter: page value must be positive integer");
            }
            if ($key === 'number' || $key === 'size') {
                continue;
            }

            $builder = $builder->{$key}($value);
        }
        return $builder;
    }
}
