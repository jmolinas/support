<?php

namespace GP\Support\Http\Middleware;

use Closure;

use GP\Support\Models\Traits\Sortable;
use GP\Support\Models\Traits\Filterable;
use GP\Support\Models\Traits\Page;
use GP\Support\Models\Traits\Searchable;

class JsonApiScope
{
    use Sortable, Filterable, Searchable, Page;
    
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
                $filter = $request->input('filter', []);
                $page = $request->input('page', []);
                $search = $request->input('search', []);
                $sort = $request->input('sort', null);
                $builder->when(
                    ! empty($filter), 
                    function ($query) use ($filter) {
                        $this->filter($filter, $query);
                    }
                )
                ->when(
                    ! empty($page), 
                    function ($query) use ($page) {
                        $this->page($page, $query);
                    }
                )
                ->when(
                    ! empty($search), 
                    function ($query) use ($search) {
                        $this->search($search, $query);
                    }
                )
                ->when(
                    ! empty($sort),
                    function ($query) use ($sort) {
                        return $this->order($query, $sort);
                    }
                );
            }
        );

        return $next($request);
    }
}
