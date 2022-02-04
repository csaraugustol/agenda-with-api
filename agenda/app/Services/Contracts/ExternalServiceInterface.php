<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;

interface ExternalServiceInterface
{
    public function sendRequest(string $postalCode): ServiceResponse;
    public function setClient(Client $client);
}
