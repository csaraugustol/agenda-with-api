<?php

namespace App\Http\Resources\Contact;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $phone = $this->firstPhoneContact();

        return [
            'id'    => $this->resource->id,
            'name'  => $this->resource->name,
            'phone' => [
                'phone_number' => $this->resource->phone_number,
            ],
        ];
    }

    /**
     * Retorna o primeiro telefone do contato
     */
    private function firstPhoneContact()
    {
        $phone = $this
            ->phones()
            ->first();

        return $phone;
    }
}
