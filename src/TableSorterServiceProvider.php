<?php

namespace Rogercbe\TableSorter;

use Illuminate\Support\ServiceProvider;

class TableSorterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'tablesorter');

        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/tablesorter'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
