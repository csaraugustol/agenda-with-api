<?php

namespace App\Services;

use App\Repositories\Contracts\TagRepository;
use App\Services\Contracts\TagServiceInterface;

class TagService extends BaseService implements TagServiceInterface
{
    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }
}
