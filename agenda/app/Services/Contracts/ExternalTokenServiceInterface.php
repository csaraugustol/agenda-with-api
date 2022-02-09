<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ExternalTokenServiceInterface
{
    public function storeToken(
        string $token,
        string $userId,
        string $system,
        bool $expiresAt,
        bool $clearRectroativicsTokens
    ): ServiceResponse;
    public function clearToken(string $userId, string $system): ServiceResponse;
}
