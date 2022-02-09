<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;
use App\Services\Params\Vexpenses\AccessTokenServiceParams;

interface VexpensesIntegrationServiceInterface
{
    public function setClient(Client $client);
    public function tokenToAccess(AccessTokenServiceParams $accessTokenServiceParams): ServiceResponse;
    public function sendRequestVExpenses(string $method, string $url, array $params = []): ServiceResponse;
}
