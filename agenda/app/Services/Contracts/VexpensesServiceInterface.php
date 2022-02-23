<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;

interface VexpensesServiceInterface
{
    public function setClient(Client $client);
    public function findAllTeamMembers(string $userId): ServiceResponse;
    public function sendRequest(string $route, string $userId): ServiceResponse;
    public function tokenToAccess(string $token, string $userId): ServiceResponse;
    public function store(string $userId, int $externalId, array $addresses): ServiceResponse;
}
