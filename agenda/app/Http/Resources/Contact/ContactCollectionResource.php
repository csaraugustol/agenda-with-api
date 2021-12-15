<?php

namespace App\Http\Resources\Contact;

use App\Http\Resources\BaseCollectionResource;

class ContactCollectionResource extends BaseCollectionResource
{
    public $collects = ContactResource::class;
}
