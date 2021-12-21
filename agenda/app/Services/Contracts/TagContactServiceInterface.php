<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface TagContactServiceInterface
{
    public function detach(string $tagId, string $contactId): ServiceResponse;
    public function attach(string $tagId, string $contactId, string $userId): ServiceResponse;
}
