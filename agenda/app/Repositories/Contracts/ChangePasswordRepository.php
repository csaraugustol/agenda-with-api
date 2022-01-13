<?php

namespace App\Repositories\Contracts;

use App\Models\ChangePassword;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ChangePasswordRepository
 * @package namespace App\Repositories\Contracts
 */
interface ChangePasswordRepository extends BaseRepositoryInterface
{
    public function model();
    public function returnAllTokensToChangePassword(string $userId): Collection;
    public function findByToken(string $token, string $userId): ?ChangePassword;
}
