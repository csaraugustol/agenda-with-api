<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserRepository
 * @package namespace App\Repositories\Contracts;
 */
interface UserRepository extends RepositoryInterface
{
    public function model();
    public function findUserByEmail(string $email): ?User;
}
