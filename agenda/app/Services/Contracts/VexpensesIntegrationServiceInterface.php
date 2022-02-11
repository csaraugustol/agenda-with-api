<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;

interface VexpensesIntegrationServiceInterface
{
    public function setClient(Client $client);
    public function tokenToAccess(string $token, string $userId): ServiceResponse;
    public function sendRequest(string $method, string $url, array $params = []): ServiceResponse;
}
