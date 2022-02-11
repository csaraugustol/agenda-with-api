<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ExternalTokenRepository
 * @package namespace App\Repositories\Contracts;
 */
interface ExternalTokenRepository extends BaseRepositoryInterface
{
    public function model();
    public function returnAllExternalTokens(string $userId, string $system): Collection;
}
