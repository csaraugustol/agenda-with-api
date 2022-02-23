<?php

namespace App\Http\Resources\Vexpenses;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Vexpenses\Phone\PhoneResource;

class TeamMembersResource extends JsonResource
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
            'external_id' => $this->resource->external_id,
            'integrated'  => $this->resource->integrated,
            'name'        => $this->resource->name,
            'email'       => $this->resource->email,
            'phones'      => PhoneResource::collection($this->resource->phones),
        ];
    }
}
