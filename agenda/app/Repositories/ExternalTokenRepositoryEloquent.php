<?php

namespace App\Repositories;

use App\Models\ExternalToken;
use Illuminate\Database\Eloquent\Collection;
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

     /**
     * Retorna todos os tokens vinculados ao usuário para acessar a integração
     * com o VExpenses
     *
     * @param string $userId
     *
     * @return Collection
     */
    public function returnAllExternalTokensSystemVExpenses(string $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('system', 'VEXPENSES')
            ->get();
    }
}
