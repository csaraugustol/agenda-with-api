<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\Address\CreateAddressServiceParams;

interface AddressServiceInterface
{
    public function find(string $addressId): ServiceResponse;
    public function store(CreateAddressServiceParams $params): ServiceResponse;
    public function update(array $params, string $addressId): ServiceResponse;
    public function delete(string $addressId): ServiceResponse;
}
