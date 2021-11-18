<?php

namespace App\Repositories;

use App\Repositories\Contracts\ContactRepository;

/**
 * Class ContactRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ContactRepositoryEloquent extends BaseRepositoryEloquent implements ContactRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return Contact::class;
    }
}
