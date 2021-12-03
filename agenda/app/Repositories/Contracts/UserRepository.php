<?php

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * Interface UserRepository
 * @package namespace App\Repositories\Contracts;
 */
interface UserRepository extends BaseRepositoryInterface
{
    public function model();
    public function findUserByEmail(string $email): ?User;
}
