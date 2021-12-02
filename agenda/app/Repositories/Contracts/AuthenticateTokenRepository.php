<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface AuthenticateTokenRepository
 * @package namespace App\Repositories\Contracts;
 */
interface AuthenticateTokenRepository extends BaseRepositoryInterface
{
    public function model();
    public function returnAllUserTokens(string $userId): Collection;
}
