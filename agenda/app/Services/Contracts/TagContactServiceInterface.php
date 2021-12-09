<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\TagContact\CreateTagContactServiceParams;

interface TagContactServiceInterface
{
    public function attach(CreateTagContactServiceParams $params): ServiceResponse;
    public function dettach(string $tagId, string $contactId): ServiceResponse;
}
