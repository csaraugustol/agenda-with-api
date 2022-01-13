<?php

namespace App\Repositories;

use App\Models\ChangePassword;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\ChangePasswordRepository;

/**
 * Class ChangePasswordRepositoryEloquent
 * @package namespace App\Repositories
 */
class ChangePasswordRepositoryEloquent extends BaseRepositoryEloquent implements ChangePasswordRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return ChangePassword::class;
    }

    /**
     * Retorna todos os tokens do usuário para alteração de senha
     *
     * @param string $userId
     *
     * @return Collection
     */
    public function returnAllTokensToChangePassword(string $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Busca um token do usuário para validar a alteração da senha
     *
     * @param string $token
     * @param string $userId
     *
     * @return ChangePassword|null
     */
    public function findByToken(string $token, string $userId): ?ChangePassword
    {
        return $this->model
            ->where('token', $token)
            ->where('user_id', $userId)
            ->first();
    }
}
