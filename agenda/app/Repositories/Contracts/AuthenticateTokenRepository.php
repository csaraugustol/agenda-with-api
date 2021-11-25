<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AuthenticateTokenRepository
 * @package namespace App\Repositories\Contracts;
 */
interface AuthenticateTokenRepository extends RepositoryInterface
{
    public function model();
}
