<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ContactRepository
 * @package namespace App\Repositories\Contracts;
 */
interface ContactRepository extends RepositoryInterface
{
    public function model();
    public function findUserByEmail(string $email): ?User;
}
