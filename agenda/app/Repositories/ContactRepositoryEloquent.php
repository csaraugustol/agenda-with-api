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
     * Busca um contato pelo id do contato
     * e do usuário logado
     *
     * @param string $contactId
     * @param string $userId
     *
     * @return Contact|null
     */
    public function findContactByUserId(string $contactId, string $userId): ?Contact
    {
        return $this->model
            ->where('id', $contactId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Retorna um contato que possui external_id
     *
     * @param string $userId
     * @param string $externalId
     *
     * @return Contact|null
     */
    public function findByContactWithExternalId(string $userId, string $externalId): ?Contact
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('external_id', $externalId)
            ->first();
    }
}
