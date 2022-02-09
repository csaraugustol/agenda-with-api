<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface ExternalTokenServiceInterface
{
    public function storeToken(
        string $token,
        string $userId,
        string $typeSystem,
        bool $expiresAt,
        bool $clearRectroativicsTokens
    ): ServiceResponse;
    public function clearToken(string $userId, string $typeSystem): ServiceResponse;
}
