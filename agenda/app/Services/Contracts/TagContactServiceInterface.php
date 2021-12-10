<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface TagContactServiceInterface
{
    public function attach(string $tagId, string $contactId): ServiceResponse;
    public function dettach(string $tagId, string $contactId): ServiceResponse;
}
