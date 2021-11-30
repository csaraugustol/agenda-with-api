<?php

namespace App\Services\Contracts;

use App\Models\User;
use App\Services\Responses\ServiceResponse;

interface AuthenticateTokenServiceInterface
{
    public function storeToken(User $user): ServiceResponse;
    public function clearToken(string $user): ServiceResponse;
}
