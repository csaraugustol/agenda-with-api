<?php

namespace App\Services;

use App\Repositories\Contracts\PhoneRepository;
use App\Services\Contracts\PhoneServiceInterface;

class PhoneService extends BaseService implements PhoneServiceInterface
{
    /**
     * @var PhoneRepository
     */
    private $phoneRepository;

    /**
     * @param PhoneRepository $phoneRepository
     */
    public function __construct(PhoneRepository $phoneRepository)
    {
        $this->phoneRepository = $phoneRepository;
    }
}
