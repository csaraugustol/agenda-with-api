<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\User\RegisterUserServiceParams;

interface UserServiceInterface
{
    public function login(string $email, string $password): ServiceResponse;
    public function register(RegisterUserServiceParams $params): ServiceResponse;
}
