<?php

namespace App\Http\Resources\Contact;

use App\Http\Resources\Tag\TagResource;
use App\Http\Resources\Phone\PhoneResource;
use App\Http\Resources\Address\AddressResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'external_id' => $this->external_id,
            'name'        => $this->name,
            'phones'      => count($this->phones) ?
                PhoneResource::collection($this->phones) : null,
            'adresses'    => count($this->adresses) ?
                AddressResource::collection($this->adresses) : null,
            'tags'        => count($this->tags) ?
                TagResource::collection($this->tags) : null,
        ];
    }
}
