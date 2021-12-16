<?php

namespace App\Repositories\Contracts;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface ContactRepository
 * @package namespace App\Repositories\Contracts;
 */
interface ContactRepository extends BaseRepositoryInterface
{
    public function model();
    public function findByUserContact(string $userId, string $contactId): ?Contact;
    public function findAllWithFilter(string $userId, string $filter = null): Collection;
}
