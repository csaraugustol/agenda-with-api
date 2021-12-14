<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface TagRepository
 * @package namespace App\Repositories\Contracts;
 */
interface TagRepository extends BaseRepositoryInterface
{
    public function model();
    public function findAll(string $userId, string $description = null): Collection;
}
