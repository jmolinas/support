<?php

namespace JMolinas\Support\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class PaginationServiceProvider extends ServiceProvider
{
    protected $page;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Paginator::currentPageResolver(function () {
            $page = $this->app['request']->input('page.number');

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return $this->page = (int) $page;
            }

            return $this->page = 1;
        });

        Paginator::currentPathResolver(function () {
            $url = $this->app['request']->fullUrl();
            $url = str_replace("page%5Bnumber%5D={$this->page}&", '', $url);
            $url = str_replace("page%5Bnumber%5D={$this->page}", '', $url);
            return $url;
        });

    }
}
