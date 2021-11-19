<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepository;

/**
 * Class TagRepositoryEloquent
 * @package namespace App\Repositories;
 */
class TagRepositoryEloquent extends BaseRepositoryEloquent implements TagRepository
{
    public function model()
    {
        return Tag::class;
    }
}
