<?php

namespace App\Http\Resources\Vexpenses;

use App\Http\Resources\BaseCollectionResource;
use App\Http\Resources\Vexpenses\TeamMembersResource;

class TeamMembersCollectionResource extends BaseCollectionResource
{
    public $collects = TeamMembersResource::class;
}
