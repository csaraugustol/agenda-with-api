<?php

namespace App\Services;

use App\Services\Responses\ServiceResponse;
use App\Repositories\Contracts\AddressRepository;
use App\Services\Contracts\AddressServiceInterface;
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

    public function find(string $addressId): ServiceResponse
    {
        return new ServiceResponse(
            true,
            '',
            null
        );
    }

    public function store(CreateAddressServiceParams $params): ServiceResponse
    {
        return new ServiceResponse(
            true,
            '',
            null
        );
    }

    public function update(array $params, string $addressId): ServiceResponse
    {
        return new ServiceResponse(
            true,
            '',
            null
        );
    }

    public function delete(string $addressId): ServiceResponse
    {
        return new ServiceResponse(
            true,
            '',
            null
        );
    }
}
