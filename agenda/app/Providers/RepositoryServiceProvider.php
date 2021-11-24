<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected $implementsName = 'Eloquent';
    protected $implementsPath = 'App\\Repositories\\';
    protected $path           = 'Repositories/Contracts';
    protected $interfacePath  = 'App\\Repositories\\Contracts\\';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Verifica se existe o path
        if (!file_exists(app_path($this->path))) {
            return false;
        }

        //Recebe todas as interfaces do diretório
        $interfaces = collect(scandir(app_path($this->path)));

        //Recebe as interfaces e faz o mapeamento retirando caracteres
        $interfaces = $interfaces->reject(function ($interface) {
            return in_array($interface, ['.', '..']);
        })
        ->map(function ($interface) {
            return str_replace('.php', '', $interface);
        });

        //Retorna o caminho da base RepositoryInterface e RepositoryEloquent
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
