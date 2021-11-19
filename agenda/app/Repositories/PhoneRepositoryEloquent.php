<?php

namespace App\Repositories;

use App\Models\Phone;
use App\Repositories\Contracts\PhoneRepository;

/**
 * Class PhoneRepositoryEloquent
 * @package namespace App\Repositories;
 */
class PhoneRepositoryEloquent extends BaseRepositoryEloquent implements PhoneRepository
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return Phone::class;
    }
}
