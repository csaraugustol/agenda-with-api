<?php

namespace App\Repositories;

use App\Models\ChangePassword;
use App\Repositories\Contracts\ChangePasswordRepository;

/**
 * Class ChangePasswordRepositoryEloquent
 * @package namespace App\Repositories
 */
class ChangePasswordRepositoryEloquent extends BaseRepositoryEloquent implements ChangePasswordRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return ChangePassword::class;
    }
}
