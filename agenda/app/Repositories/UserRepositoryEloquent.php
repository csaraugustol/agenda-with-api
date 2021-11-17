<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepository;

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
}
