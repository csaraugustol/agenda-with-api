<?php

namespace App\Http\Resources\Vexpenses;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'id'                      => $this->resource->id,
            'external_id'             => $this->resource->external_id,
            'name'                    => $this->resource->name,
            'email'                   => $this->resource->email,
            'phone1'                  => $this->resource->phone1,
            'phone2'                  => $this->resource->phone2,
        ];
    }
}
