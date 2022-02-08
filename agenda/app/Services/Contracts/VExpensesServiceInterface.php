<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;

interface VExpensesServiceInterface
{
    public function setClient(Client $client);
    public function tokenToAccessVExpenses(string $userId): ServiceResponse;
    public function sendRequestVExpenses(string $method, string $url, array $params = []): ServiceResponse;
}
