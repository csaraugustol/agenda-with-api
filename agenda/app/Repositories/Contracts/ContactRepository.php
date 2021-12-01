<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ContactRepository
 * @package namespace App\Repositories\Contracts;
 */
interface ContactRepository extends RepositoryInterface
{
    public function model();
}
