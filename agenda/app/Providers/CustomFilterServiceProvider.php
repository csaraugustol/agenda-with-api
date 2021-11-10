<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomFilterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (class_exists('Waavi\Sanitizer\Laravel\SanitizerServiceProvider')) {
            //Recebe todos os filtros
            $filters = collect(scandir(app_path('Filters')));

            $filters->reject(function ($filter) {
                return in_array($filter, ['.', '..']);
            })
            ->map(function ($filter) {
                return 'App\\Filters\\' . str_replace('.php', '', $filter);
            })
            ->each(function ($filter) {
                $filter = new $filter();
                Sanitazer::extend($filter->name, get_class($filter));
            });
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
