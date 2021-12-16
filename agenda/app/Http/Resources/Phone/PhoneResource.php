<?php

namespace App\Http\Resources\Phone;

use Illuminate\Http\Resources\Json\JsonResource;

class PhoneResource extends JsonResource
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
            'id'           => $this->resource->id,
            'phone_number' => $this->resource->phone_number,
        ];
    }
}
