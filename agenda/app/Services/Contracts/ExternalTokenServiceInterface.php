<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ExternalTokenServiceInterface
{
    public function storeToken(string $userId): ServiceResponse;
    public function clearToken(string $userId): ServiceResponse;
}
