<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Services\Responses\InternalError;
use GuzzleHttp\Exception\RequestException;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\ExternalServiceInterface;

class ExternalService extends BaseService implements ExternalServiceInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $client = new Client([
            'base_uri' => 'https://viacep.com.br/ws/',
            'headers'  => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->setClient($client);
    }

    /**
     * Realiza a requisição na API ViaCep
     *
     * @param string $postalCode
     *
     * @return ServiceResponse
     */
    public function sendRequest(string $postalCode): ServiceResponse
    {
        try {
            $response = $this->client->get($postalCode . '/json');

            $data = json_decode((string) $response->getBody());

            if (array_key_exists("erro", $data)) {
                return new ServiceResponse(
                    false,
                    'O cep informado é inválido.',
                    null,
                    [
                        new InternalError(
                            'O cep informado é inválido.',
                            16
                        )
                    ]
                );
            }
        } catch (RequestException $requestError) {
            if ($requestError->getCode() === 400) {
                return new ServiceResponse(
                    false,
                    'O cep informado é inválido.',
                    null,
                    [
                        new InternalError(
                            'O cep informado é inválido.',
                            16
                        )
                    ]
                );
            }

            return new ServiceResponse(
                false,
                'Requisição inválida.',
                null,
                [
                    new InternalError(
                        'Requisição inválida.',
                        17
                    )
                ]
            );
        }

        return new ServiceResponse(
            true,
            'A requisição foi realizada com sucesso.',
            $data
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
