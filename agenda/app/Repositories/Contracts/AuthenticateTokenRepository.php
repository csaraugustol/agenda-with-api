<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AuthenticateTokenRepository
 * @package namespace App\Repositories\Contracts;
 */
interface AuthenticateTokenRepository extends RepositoryInterface
{
    public function model();
    public function verifyExistsToken(string $id): Collection;
}
