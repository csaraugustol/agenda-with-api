<?php

namespace App\Repositories;

use App\Models\AuthenticateToken;
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
}
