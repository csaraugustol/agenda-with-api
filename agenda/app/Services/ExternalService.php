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
     * Realiza a requisição na API ViaCep
     *
     * @param string $postalCode
     *
     * @return ServiceResponse
     */
    public function sendRequest(string $postalCode): ServiceResponse
    {
        try {
            $client = new Client();

            $response = $client->get('viacep.com.br/ws/' . $postalCode . '/json');

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
}
