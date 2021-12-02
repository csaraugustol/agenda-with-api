<?php

namespace App\Repositories\Contracts;

use App\Models\AuthenticateToken;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface AuthenticateTokenRepository
 * @package namespace App\Repositories\Contracts;
 */
interface AuthenticateTokenRepository extends BaseRepositoryInterface
{
    public function model();
    public function findByToken(string $token): AuthenticateToken;
    public function returnAllUserTokens(string $userId): Collection;
}
