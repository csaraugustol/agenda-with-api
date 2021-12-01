<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;

/**
 * Class UserRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepositoryEloquent implements UserRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Realiza busca no banco e retorna um usuário buscando pelo email
     *
     * @param string $email
     *
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }
}
