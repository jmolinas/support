<?php

namespace JMolinas\Support\Http\Middleware;

use JMolinas\Support\Http\Exceptions\InvalidUrlParameterException;
use Closure;

use JMolinas\Support\Models\Traits\Sortable;
use JMolinas\Support\Models\Traits\Filterable;
use JMolinas\Support\Models\Traits\Row;
use JMolinas\Support\Models\Traits\Searchable;
use Illuminate\Http\Request;

class JsonApiScope
{
    use Sortable, Filterable, Searchable, Row;

    /**
     * Handle incoming request
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $class)
    {
        $class::addGlobalScope(
            'api',
            function ($builder) use ($request) {
                $filter = $this->validateParamArray($request, 'filter');
                $row =  $this->validateParamArray($request, 'page');
                $search =  $this->validateParamArray($request, 'search');
                $sort = $request->input('sort', null);
                $builder->when(
                    !empty($filter),
                    function ($query) use ($filter) {
                        $this->filter($filter, $query);
                    }
                )
                    ->when(
                        !empty($row),
                        function ($query) use ($row) {
                            $this->page($row, $query);
                        }
                    )
                    ->when(
                        !empty($search),
                        function ($query) use ($search) {
                            $this->search($search, $query);
                        }
                    )
                    ->when(
                        !empty($sort),
                        function ($query) use ($sort) {
                            return $this->order($query, $sort);
                        }
                    );
            }
        );

        return $next($request);
    }

    /**
     * Validate Parameter Must be Array
     *
     * @param Request $request
     * @param string $param
     *
     * @return array|InvalidUrlParameterException
     */
    protected function validateParamArray(Request $request, string $param): array
    {
        $value = $request->input($param, []);
        if (is_array($value)) {
            return $value;
        }
        throw new InvalidUrlParameterException("Invalid Parameter: {$param} must be array");
    }
}
