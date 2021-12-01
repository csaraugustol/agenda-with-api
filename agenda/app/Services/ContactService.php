<?php

namespace App\Services;

use App\Repositories\Contracts\ContactRepository;
use App\Services\Contracts\ContactServiceInterface;

class ContactService extends BaseService implements ContactServiceInterface
{
    /**
     * @var ContactRepository
     */
    private $contactRepository;

    /**
     * @param ContactRepository $contactRepository
     */
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }
}
