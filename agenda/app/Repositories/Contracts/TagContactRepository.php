<?php

namespace App\Repositories\Contracts;

use App\Models\TagContact;

/**
 * Interface TagContactRepository
 * @package namespace App\Repositories\Contracts;
 */
interface TagContactRepository extends BaseRepositoryInterface
{
    public function model();
    public function findTagContact(string $tagId, string $contactId): ?TagContact;
    public function verifyExistsDeletedAttach(string $tagId, string $contactId): ?TagContact;
}
