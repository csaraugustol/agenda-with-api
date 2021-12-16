<?php

namespace App\Http\Resources\Contact;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactIndexResource extends JsonResource
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
            'id'    => $this->id,
            'name'  => $this->name,
            'phone_number' => $phone->phone_number,
        ];
    }

    /**
     * Retorna o primeiro telefone do contato
     */
    private function firstPhoneContact()
    {
        return $this
            ->phones()
            ->orderBy('created_at')
            ->first();
    }
}
