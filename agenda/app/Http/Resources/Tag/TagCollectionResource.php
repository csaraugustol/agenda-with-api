<?php

namespace App\Http\Resources\Tag;

use App\Http\Resources\BaseCollectionResource;

class TagCollectionResource extends BaseCollectionResource
{
    public $collects = TagResource::class;
}
