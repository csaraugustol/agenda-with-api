<?php

namespace App\Services;

use App\Repositories\Contracts\AddressRepository;
use App\Services\Contracts\AddressServiceInterface;

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
}
