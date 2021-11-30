<?php

namespace App\Repositories;

use App\Models\TagContact;
use App\Repositories\Contracts\TagContactRepository;

/**
 * Class TagContactRepositoryEloquent
 * @package namespace App\Repositories;
 */
class TagContactRepositoryEloquent extends BaseRepositoryEloquent implements TagContactRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return TagContact::class;
    }
}
