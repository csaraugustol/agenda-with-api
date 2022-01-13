<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ChangePasswordServiceInterface
{
    public function newToken(string $userId): ServiceResponse;
    public function clearToken(string $userId): ServiceResponse;
    public function findByToken(string $token, string $userId): ServiceResponse;
}
