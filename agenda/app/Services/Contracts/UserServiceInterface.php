<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;
use App\Services\Params\User\RegisterUserServiceParams;

interface UserServiceInterface
{
    public function find(string $userId): ServiceResponse;
    public function logout(string $userId): ServiceResponse;
    public function findByEmail(string $email): ServiceResponse;
    public function update(array $params, string $userId): ServiceResponse;
    public function tokenToChangePassword(string $userId): ServiceResponse;
    public function login(string $email, string $password): ServiceResponse;
    public function register(RegisterUserServiceParams $params): ServiceResponse;
}
