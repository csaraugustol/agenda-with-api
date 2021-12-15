<?php

namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * Retorna todos os contatos do usuário podendo
     * haver filtragem por nome e telefone
     *
     * @param string $userId
     * @param array $filters
     *
     * @return Collection
     */
    public function findAllWithFilter(string $userId, array $filters = []): Collection
    {
        $query = $this->model
            ->select('contacts.*')
            ->join('phones', 'phones.contact_id', '=', 'contacts.id')
            ->where('user_id', $userId);

        if ($filters['phone_number']) {
            $query->where('phone_number', 'like', '%' . $filters['phone_number'] . '%');
        }

        if ($filters['name']) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query->get();
    }
}
