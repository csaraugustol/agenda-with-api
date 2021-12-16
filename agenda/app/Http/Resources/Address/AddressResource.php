<?php

namespace App\Http\Resources\Address;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'id'           =>  $this->resource->id,
            'street_name'  =>  $this->resource->street_name,
            'number'       =>  $this->resource->number,
            'complement'   =>  $this->resource->complement,
            'neighborhood' =>  $this->resource->neighborhood,
            'city'         =>  $this->resource->city,
            'state'        =>  $this->resource->state,
            'postal_code'  =>  $this->resource->postal_code,
            'country'      =>  $this->resource->country,
        ];
    }
}
