<?php

namespace App\Http\Resources\Vexpenses;

use Illuminate\Http\Resources\Json\JsonResource;

class VexpensesResource extends JsonResource
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
            'token'  => $this->resource->token,
        ];
    }
}
