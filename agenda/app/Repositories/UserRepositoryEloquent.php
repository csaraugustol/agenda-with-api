<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
     * @return Collection
     */
    public function findUserByEmail(string $email): Collection
    {
        return $this->model
            ->where('email', $email)
            ->get();
    }
}
