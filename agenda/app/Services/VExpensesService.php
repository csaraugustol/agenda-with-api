<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Services\Responses\InternalError;
use GuzzleHttp\Exception\RequestException;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\VExpensesServiceInterface;

class VExpensesService extends BaseService implements VExpensesServiceInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $client = new Client([
            'base_uri' => config('auth.vexpenses_base_url'),
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . config('auth.vexpenses_token'),
            ],
            'verify' => env('APP_ENV') === 'production' ? true : false,
        ]);

        $this->setClient($client);
    }

    /**
     * Envio de Requisição para VExpenses
     *
     * @param  string   $method
     * @param  string   $url
     * @param  array    $params
     *
     * @return ServiceResponse
     */
    public function sendRequestVexpenses(
        string $method,
        string $url,
        array $params = []
    ): ServiceResponse {
        try {
            $options = [];
            $method = strtolower($method);

            if (!empty($params)) {
                $optionsType = $method !== 'get' ? 'json' : 'query';
                $options[$optionsType] = $params;
            }

            $response = $this->client->{$method}($url, $options);

            $body = json_decode((string) $response->getBody());
        } catch (RequestException $requestError) {
            $errorResponse = $requestError->getResponse();

            if ($requestError->getCode() === 500) {
                $body = $errorResponse->getBody();

                return $this->defaultErrorReturn(
                    $requestError,
                    compact('method', 'url', 'params', 'body')
                );
            }

            $response = json_decode((string) $errorResponse->getBody());
            $response->code = $requestError->getCode();

            $this->defaultErrorReturn(
                $requestError,
                compact('method', 'url', 'params', 'response')
            );

            if ($response->code === 401) {
                return new ServiceResponse(
                    false,
                    'O token é inválido!',
                    $response,
                    [
                        new InternalError(
                            'O token é inválido!',
                            25
                        )
                    ]
                );
            }

            return new ServiceResponse(
                false,
                'Houve uma falha na requisição.',
                $response
            );
        }

        return new ServiceResponse(
            true,
            'Requisição realiza com sucesso.',
            $body
        );
    }

    /**
     * Seta o client
     *
     * @param Client $client
     *
     * @return void
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}