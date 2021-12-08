<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\Phone\CreatePhoneServiceParams;

interface PhoneServiceInterface
{
    public function find(string $phoneId): ServiceResponse;
    public function store(CreatePhoneServiceParams $params): ServiceResponse;
    public function update(array $params, string $phoneId): ServiceResponse;
    public function delete(string $phoneId): ServiceResponse;
}
