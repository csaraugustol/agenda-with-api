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
     * @param string      $userId
     * @param string|null $filters
     *
     * @return Collection
     */
    public function findAllWithFilter(string $userId, string $filter = null): Collection
    {
        $query = $this->model
            ->select('contacts.*')
            ->join('phones', 'phones.contact_id', '=', 'contacts.id')
            ->where('user_id', $userId);

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('phone_number', 'like', '%' . $filter . '%');
                $q->orWhere('name', 'like', '%' . $filter . '%');
            });
        }

        return $query->get();
    }

    /**
     * Verifica e conta se existe um nome de contato
     * já registrado para o usuário
     *
     * @param string $userId
     * @param string $contactName
     *
     * @return integer
     */
    public function verifyExistsContactNameRegisteredUser(string $contactName, string $userId): int
    {
        return $this->model
            ->where('name', $contactName)
            ->where('user_id', $userId)
            ->count();
    }
}
