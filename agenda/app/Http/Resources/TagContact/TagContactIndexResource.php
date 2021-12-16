<?php

namespace App\Http\Resources\TagContact;

use Illuminate\Http\Resources\Json\JsonResource;

class TagContactIndexResource extends JsonResource
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
            'id'         => $this->resource->id,
            'tag_id'     => $this->resource->tag_id,
            'contact_id' => $this->resource->contact_id,
        ];
    }
}
