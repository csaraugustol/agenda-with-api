<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ContactServiceInterface
{
    public function find(string $contactId): ServiceResponse;
    public function findByUserContact(string $userId, string $contactId): ServiceResponse;
    public function findAllWithFilter(string $userId, string $filter = null): ServiceResponse;
}
