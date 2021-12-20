<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\Contact\CreateContactServiceParams;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

interface ContactServiceInterface
{
    public function find(string $contactId): ServiceResponse;
    public function store(CreateContactServiceParams $params): ServiceResponse;
    public function findByUserContact(string $userId, string $contactId): ServiceResponse;
    public function findAllWithFilter(string $userId, string $filter = null): ServiceResponse;
    public function storeCompleteContacts(CreateCompleteContactsServiceParams $params): ServiceResponse;
    public function verifyExistsContactNameRegisteredUser(string $contactName, string $userId): ServiceResponse;
}
