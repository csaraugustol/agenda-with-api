<?php

namespace App\Repositories;

use App\Models\ExternalToken;
use App\Services\Contracts\ExternalTokenServiceInterface;

/**
 * Class ExternalTokenRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ExternalTokenRepositoryEloquent extends BaseRepositoryEloquent implements ExternalTokenServiceInterface
{
    /**
     * Retorna nome da model
     *
     * @return string
     */
    public function model()
    {
        return ExternalToken::class;
    }
}
