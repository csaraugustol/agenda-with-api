<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ExternalServiceInterface
{
    public function sendRequest(string $postalCode): ServiceResponse;
}
