<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

interface ContactServiceInterface
{
    public function find(string $contactId, string $userId): ServiceResponse;
    public function delete(string $contactId, string $userId): ServiceResponse;
    public function store(CreateCompleteContactsServiceParams $params): ServiceResponse;
    public function findAllWithFilter(string $userId, string $filter = null): ServiceResponse;
    public function update(string $contactName, string $contactId, string $userId): ServiceResponse;
}
