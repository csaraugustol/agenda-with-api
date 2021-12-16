<?php

namespace App\Http\Resources\Contact;

use App\Http\Resources\Phone\PhoneResource;
use App\Http\Resources\Address\AddressResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TagContact\TagContactIndexResource;

class ContactShowResource extends JsonResource
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
            'id'       => $this->id,
            'user_id'  => $this->user_id,
            'name'     => $this->name,
            'phones'   => count($this->phones) ?
                PhoneResource::collection($this->phones) : null,
            'adresses' => count($this->adresses) ?
                AddressResource::collection($this->adresses) : null,
            'tags'     => count($this->tagcontacts) ?
                TagContactIndexResource::collection($this->tagcontacts) : null,
        ];
    }
}
