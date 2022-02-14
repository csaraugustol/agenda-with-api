<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;

interface VexpensesServiceInterface
{
    public function setClient(Client $client);
    public function tokenToAccess(string $token, string $userId): ServiceResponse;
    public function sendRequest(string $route): ServiceResponse;
    public function findAllTeamMembers(string $route): ServiceResponse;
}
