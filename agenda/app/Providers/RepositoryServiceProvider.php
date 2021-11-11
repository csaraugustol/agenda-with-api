<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected $implementsName = 'Eloquent';
    protected $implementsPath = 'App\\Repositories\\';
    protected $path           = 'Repositories/Contacts';
    protected $interfacePath  = 'App\\Repositories\\Contacts';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Verifica se existe o path
        if (!file_exists(app_path($path))) {
            return false;
        }

        //Recebe todas as interfaces do diretório
        $interfaces = collect(scandir(app_path($path)));

        $interfaces = $interfaces->reject(function ($interface) {
            return in_array($interface, ['.', '..']);
        })
            ->map(function ($interface) {
                return str_replace('.php', '', $interface);
            });

        $interfaces->each(function ($interface) {
            $this->app->bind(
                $this->interfacePath  . $interface,
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
