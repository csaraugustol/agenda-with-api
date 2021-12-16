<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ContactServiceInterface
{
    public function findAllWithFilter(string $userId, string $filter = null): ServiceResponse;
}
