<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;
use App\Services\Responses\ServiceResponse;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

interface VexpensesServiceInterface
{
    public function setClient(Client $client);
    public function findAllTeamMembers(string $userId): ServiceResponse;
    public function sendRequest(string $route, string $userId): ServiceResponse;
    public function tokenToAccess(string $token, string $userId): ServiceResponse;
    public function findTeamMember(string $userId, string $externalId): ServiceResponse;
    public function store(CreateCompleteContactsServiceParams $params): ServiceResponse;
}
