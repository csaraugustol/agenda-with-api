<?php

namespace App\Repositories;

use App\Models\AuthenticateToken;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\AuthenticateTokenRepository;

/**
 * Class AuthenticateTokenRepositoryEloquent
 * @package namespace App\Repositories;
 */
class AuthenticateTokenRepositoryEloquent extends BaseRepositoryEloquent implements AuthenticateTokenRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return AuthenticateToken::class;
    }

    /**
     * Retorna todos os tokens relacionados ao usuário
     *
     * @param string $userId
     *
     * @return Collection
     */
    public function returnAllUserTokens(string $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Retorna o último token do usuário para validação de acesso
     *
     * @param string $token
     *
     * @return AuthenticateToken|null
     */
    public function findByToken(string $token): ?AuthenticateToken
    {
        return $this->model
            ->where('token', $token)
            ->first();
    }
}
