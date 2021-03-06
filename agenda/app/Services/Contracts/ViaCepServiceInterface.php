<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;

interface ViaCepServiceInterface
{
    public function sendRequestViaCep(string $postalCode): ServiceResponse;
    public function setClient(Client $client);
}
