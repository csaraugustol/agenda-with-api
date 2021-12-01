<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Recebe o caminho das services para que
     * ela possa ser encontrada e ter seu
     * serviço utilizado
     *
     * @var array
     */
    protected $implementsPaths = [
        'default' => 'App\\Services\\'
    ];

    /**
     * Recebe o caminho das interfaces para
     * que ela possa ser usada pela service
     *
     * @var array
     */
    protected $interfacePaths = [
        'default' => 'App\\Services\\Contracts\\'
    ];

    /**
     * Recebe os diretórios das services
     * que serão utilizadas
     *
     * @var array
     */
    protected $serviceDirectoriesPaths = [
        'default' => 'Services/Contracts'
    ];

    /**
     * Register services.
      *
     * @return void
     */
    public function register()
    {
        //Para cada service solicitada é aplicada uma função e suas condições
        array_walk($this->implementsPaths, function ($implementsPath, $identifier) {
            // Verifica se existe o diretório
            if (!file_exists(app_path($this->serviceDirectoriesPaths[$identifier]))) {
                return false;
            }

            // Recebe todas as interfaces
            $interfaces = collect(scandir(app_path($this->serviceDirectoriesPaths[$identifier])));

            // Recebe as interfaces pelo nome e tira a extensão do arquivo
            $interfaces = $interfaces->reject(function ($interface) {
                return in_array($interface, ['.', '..']);
            })
            ->map(function ($interface) {
                return str_replace('.php', '', $interface);
            });

            //Realizando o bind da classe que irá implementar a interface
            $interfaces->each(function ($interfaceClassName) use ($implementsPath, $identifier) {
                $serviceClassName = str_replace('Interface', '', $interfaceClassName);

                $pathInterfaceClass = $this->interfacePaths[$identifier] . $interfaceClassName;
                $pathImplementationClass = $implementsPath . $serviceClassName;

                $this->app->bind(
                    $pathInterfaceClass,
                    $pathImplementationClass
                );
            });
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
