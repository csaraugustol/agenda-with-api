<?php

namespace App\Repositories;

use App\Models\ExternalToken;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\ExternalTokenRepository;

/**
 * Class ExternalTokenRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ExternalTokenRepositoryEloquent extends BaseRepositoryEloquent implements ExternalTokenRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return ExternalToken::class;
    }

     /**
     * Retorna todos os tokens vinculados ao usuário para acessar a integração
     *
     * @param string $userId
     * @param string $system
     *
     * @return Collection
     */
    public function returnAllExternalTokens(string $userId, string $system): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('system', $system)
            ->get();
    }

    /**
     * Retorna o token da integração
     *
     * @param string $userId
     * @param string $system
     *
     * @return ExternalToken|null
     */
    public function findByToken(string $userId, string $system): ?ExternalToken
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('system', $system)
            ->first();
    }
}
