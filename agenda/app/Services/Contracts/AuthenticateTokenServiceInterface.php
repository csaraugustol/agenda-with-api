<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface AuthenticateTokenServiceInterface
{
    public function findToken(string $token): ServiceResponse;
    public function storeToken(string $userId): ServiceResponse;
    public function clearToken(string $userId): ServiceResponse;
}
