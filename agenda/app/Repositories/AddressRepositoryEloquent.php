<?php

namespace App\Repositories;

use App\Repositories\Contracts\AddressRepository;

/**
 * Class AddressRepositoryEloquent
 * @package namespace App\Repositories;
 */
class AddressRepositoryEloquent extends BaseRepositoryEloquent implements AddressRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return Address::class;
    }
}
