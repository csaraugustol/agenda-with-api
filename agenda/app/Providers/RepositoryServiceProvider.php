<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected $implementsPath = 'App\\Repositories\\';
    protected $implementsName = 'Eloquent';
    protected $interfacePath  = 'App\\Repositories\\Contacts';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (!file_exists(app_path('Repositories/Contacts'))) {
            return false;
        }

        $interfaces = collect(scandir(app_path('Repositories/Contacts')));

        $interfaces = $interfaces->reject(function ($interface) {
            return in_array($interface, ['.', '..']);
            })
                ->map(function ($interface) {
                    return str_replace('.php', '', $interface);
                });

            $interfaces->each(function ($interface) {
                $this->app->bind(
                    $this->interfacePath . $interface,
                    $this->implementsPath . $interface . $this->implementsName
                );
            });
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
