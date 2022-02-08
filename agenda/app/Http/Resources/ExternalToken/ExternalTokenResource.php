<?php

namespace App\Http\Resources\ExternalToken;

use Illuminate\Http\Resources\Json\JsonResource;

class ExternalTokenResource extends JsonResource
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
            'token'      => $this->resource->token,
            'expires_at' => $this->resource->expires_at,
            'system'     => $this->resource->system,
        ];
    }
}
