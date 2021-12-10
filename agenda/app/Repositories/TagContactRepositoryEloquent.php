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
     * Busca e retorna uma TagContact
     *
     * @param string $tagId
     * @param string $contactId
     * @param boolean $withTrashed
     *
     * @return TagContact|null
     */
    public function findTagContact(
        string $tagId,
        string $contactId,
        bool $withTrashed = false
    ): ?TagContact {
        $query =  $this->model
            ->where('tag_id', $tagId)
            ->where('contact_id', $contactId);
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->first();
    }
}
