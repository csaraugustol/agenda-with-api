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

    /**
     * Verifica se existe o vinculo deletado entre a tag e o contato
     *
     * @param string $tagId
     * @param string $contactId
     *
     * @return TagContact|null
     */
    public function verifyExistsDeletedAttach(string $tagId, string $contactId): ?TagContact
    {
        return $this->model
            ->where('tag_id', $tagId)
            ->where('contact_id', $contactId)
            ->withTrashed()
            ->get();
    }

     /**
     * Busca e retorna uma TagContact
     *
     * @param string $tagId
     * @param string $contactId
     *
     * @return TagContact|null
     */
    public function findTagContact(string $tagId, string $contactId): ?TagContact
    {
        return $this->model
            ->where('tag_id', $tagId)
            ->where('contact_id', $contactId)
            ->get();
    }
}
