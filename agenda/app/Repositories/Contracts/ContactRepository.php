<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ContactRepository
 * @package namespace App\Repositories\Contracts;
 */
interface ContactRepository extends BaseRepositoryInterface
{
    public function model();
    public function findAllWithFilter(string $userId, string $filter = null): Collection;
}
