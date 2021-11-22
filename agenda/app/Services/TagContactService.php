<?php

namespace App\Services;

use App\Repositories\Contracts\TagContactRepository;
use App\Services\Contracts\TagContactServiceInterface;

class TagContactService extends BaseService implements TagContactServiceInterface
{
    /**
     * @var TagContactRepository
     */
    private $tagContactRepository;

    /**
     * @param TagContactRepository $tagContactRepository
     */
    public function __construct(TagContactRepository $tagContactRepository)
    {
        $this->tagContactRepository = $tagContactRepository;
    }
}
