<?php

namespace App\Repositories;

use App\Models\ExternalToken;
use App\Repositories\Contracts\ExternalTokenRepository;

/**
 * Class ExternalTokenRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ExternalTokenRepositoryEloquent extends BaseRepositoryEloquent implements ExternalTokenRepository
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
