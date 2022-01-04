<?php

namespace App\Services;

use App\Repositories\Contracts\ChangePasswordRepository;
use App\Services\Contracts\ChangePasswordServiceInterface;

class ChangePasswordService extends BaseService implements ChangePasswordServiceInterface
{
    /**
     * @var ChangePasswordRepository
     */
    private $changePasswordRepository;

    /**
     * @param ChangePasswordRepository $changePasswordRepository
     */
    public function __construct(ChangePasswordRepository $changePasswordRepository)
    {
        $this->changePasswordRepository = $changePasswordRepository;
    }
}
