<?php

namespace App\Services\Contracts;

use Carbon\Carbon;
use App\Services\Responses\ServiceResponse;

interface ExternalTokenServiceInterface
{
    public function storeToken(
        string $token,
        string $userId,
        string $system,
        Carbon $expiresAt = null,
        bool $clearRectroativicsTokens = true
    ): ServiceResponse;
    public function clearToken(string $userId, string $system): ServiceResponse;
    public function findByToken(string $userId, string $system): ServiceResponse;
}
