<?php

namespace App\Services;

use App\Repositories\Contracts\ExternalTokenRepository;
use App\Services\Contracts\ExternalTokenServiceInterface;

class ExternalTokenService extends BaseService implements ExternalTokenServiceInterface
{
    /**
     * @var ExternalTokenRepository
     */
    private $externalTokenRepository;

    /**
     * @param ExternalTokenRepository $externalTokenRepository
     */
    public function __construct(ExternalTokenRepository $externalTokenRepository)
    {
        $this->externalTokenRepository = $externalTokenRepository;
    }
}
