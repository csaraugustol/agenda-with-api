<?php

namespace App\Services;

use Throwable;
use App\Services\Responses\InternalError;
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
                    'Não é possível realizar a atualização do endereço.',
                    null,
                    [
                        new InternalError(
                            'Não é possível realizar a atualização do endereço.',
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
     *
     * @return ServiceResponse
     */
    public function delete(string $addressId): ServiceResponse
    {
        try {
            $findAddressResponse = $this->find($addressId);
            if (!$findAddressResponse->success || is_null($findAddressResponse)) {
                return new ServiceResponse(
                    false,
                    $findAddressResponse->message,
                    null,
                    $findAddressResponse->internalErrors
                );
            }

            $this->addressRepository->delete($addressId);
        } catch (Throwable $throwable) {
            return $this->defaultErrorReturn($throwable, compact('addressId'));
        }

        return new ServiceResponse(
            true,
            'Endereço removido com sucesso.',
            null
        );
    }
}
