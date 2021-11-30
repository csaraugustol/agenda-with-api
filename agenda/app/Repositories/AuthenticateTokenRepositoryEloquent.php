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
     * Undocumented function
     *
     * @return Collection
     */
    public function verifyExistsToken(string $id): Collection
    {
        return $this->model
            ->where('user_id', $id)
            ->get();
    }
}
