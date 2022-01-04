<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ChangePasswordRepository
 * @package namespace App\Repositories\Contracts
 */
interface ChangePasswordRepository extends BaseRepositoryInterface
{
    public function model();
    public function returnAllTokensToChangePassword(string $userId): Collection;
}
