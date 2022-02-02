<?php

namespace App\Services;

use Throwable;
use GuzzleHttp\Client;
use App\Services\Responses\InternalError;
use GuzzleHttp\Exception\RequestException;
use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\AddressRepository;
use App\Services\Contracts\AddressServiceInterface;
use App\Services\Contracts\ContactServiceInterface;
use App\Services\Params\Address\CreateAddressServiceParams;

class AddressService extends BaseService implements AddressServiceInterface
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;

    /**
     * @param AddressRepository $addressRepository
     */
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * Busca um endereço pelo id
     *
     * @param string $addressId
     *
     * @return ServiceResponse
     */
    public function find(string $addressId): ServiceResponse
    {
        try {
            $address = $this->addressRepository->findOrNull($addressId);

            if (is_null($address)) {
                return new ServiceResponse(
                    true,
                    'Endereço não encontrado.',
                    null,
                    [
                        new InternalError(
                            'Endereço não encontrado.',
                            9
                        )
                    ]
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('addressId'));
        }

        return new ServiceResponse(
            true,
            'Endereço encontrado com sucesso.',
            $address
        );
    }

    /**
     * Criação de um novo endereço
     *
     * @param CreateAddressServiceParams $params
     *
     * @return ServiceResponse
     */
    public function store(CreateAddressServiceParams $params): ServiceResponse
    {
        try {
            $address = $this->addressRepository->create($params->toArray());
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params'));
        }
        return new ServiceResponse(
            true,
            'Endereço cadastrado com sucesso.',
            $address
        );
    }

    /**
     * Realiza atualização do endereço
     *
     * @param array  $params
     * @param string $addressId
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function update(array $params, string $addressId, string $userId): ServiceResponse
    {
        try {
            $findAddressResponse = $this->find($addressId);
            if (!$findAddressResponse->success || is_null($findAddressResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findAddressResponse->message,
                    null,
                    $findAddressResponse->internalErrors
                );
            }

            $address = $findAddressResponse->data;
            $findContactResponse = app(ContactServiceInterface::class)
                ->find($address->contact_id, $userId);

            if (is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    'O endereço não foi encontrado.',
                    null,
                    [
                        new InternalError(
                            'O endereço não foi encontrado.',
                            15
                        )
                    ]
                );
            }

            $addressUpdate = $this->addressRepository->update(
                $params,
                $addressId
            );
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('params', 'addressId', 'userId'));
        }

        return new ServiceResponse(
            true,
            'Endereço atualizado com sucesso.',
            $addressUpdate
        );
    }

    /**
     * Deleta um endereço pelo id
     *
     * @param string $addressId
     * @param string $userId
     *
     * @return ServiceResponse
     */
    public function delete(string $addressId, string $userId): ServiceResponse
    {
        try {
            $findAddressResponse = $this->find($addressId);
            if (!$findAddressResponse->success || is_null($findAddressResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findAddressResponse->message,
                    null,
                    $findAddressResponse->internalErrors
                );
            }

            $address = $findAddressResponse->data;
            $findContactResponse = app(ContactServiceInterface::class)
                ->find($address->contact_id, $userId);

            if (is_null($findContactResponse->data)) {
                return new ServiceResponse(
                    false,
                    'O endereço não foi encontrado.',
                    null,
                    [
                        new InternalError(
                            'O endereço não foi encontrado.',
                            15
                        )
                    ]
                );
            }

            $this->addressRepository->delete($addressId);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('addressId', 'userId'));
        }

        return new ServiceResponse(
            true,
            'Endereço removido com sucesso.',
            null
        );
    }

    /**
     * Busca os dados de um cep na
     * API ViaCep e retorna o endereço
     *
     * @param string $postalCode
     *
     * @return ServiceResponse
     */
    public function findByPostalCode(string $postalCode): ServiceResponse
    {
        try {
            $findByPostalCodeResponse = $this->sendRequest($postalCode);

            if (!$findByPostalCodeResponse->success || is_null($findByPostalCodeResponse->data)) {
                return new ServiceResponse(
                    false,
                    $findByPostalCodeResponse->message,
                    null,
                    $findByPostalCodeResponse->internalErrors
                );
            }
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('postalCode'));
        }

        return new ServiceResponse(
            true,
            'O Endereço foi encontrado pelo CEP.',
            $findByPostalCodeResponse->data
        );
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
