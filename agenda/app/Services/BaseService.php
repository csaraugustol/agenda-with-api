<?php

namespace App\Services;

use Throwable;
use GuzzleHttp\Client;
use App\Exceptions\PolicyException;
use App\Services\Responses\InternalError;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\BaseServiceInterface;

class BaseService implements BaseServiceInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $client = new Client([
            'base_uri' => 'https://viacep.com.br/',
            'headers'  => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->setClient($client);
    }

    /**
     * Retorno do erro padrão em caso de erro nas services
     *
     * @param Throwable|PolicyException $exception
     * @param array|string $data
     *
     * @return ServiceResponse
     */
    protected function defaultErrorReturn(
        Throwable $exception,
        $data = null
    ): ServiceResponse {
        // Tratando o caso de erro de validação usando as políticas
        if ($exception instanceof PolicyException) {
            return new ServiceResponse(
                false,
                'POLICY_VALIDATION_ERROR',
                null,
                [new InternalError(
                    $exception->getMessage(),
                    $exception->getCode()
                )]
            );
        }

        return new ServiceResponse(
            false,
            __('services/base.unknow_error_try_again'),
            $data
        );
    }

    /**
     * Seta o client
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
