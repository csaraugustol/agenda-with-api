<?php

namespace App\Repositories;

use App\Models\ChangePassword;
use App\Repositories\Contracts\ChangePasswordRepository;
use Illuminate\Database\Eloquent\Collection;

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
}
