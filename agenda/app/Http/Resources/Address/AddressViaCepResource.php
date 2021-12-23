<?php

namespace App\Http\Resources\Address;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressViaCepResource extends JsonResource
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
            'street_name'  =>  $this->resource['logradouro'],
            'neighborhood' =>  $this->resource['bairro'],
            'city'         =>  $this->resource['localidade'],
            'state'        =>  $this->resource['uf'],
            'postal_code'  =>  $this->resource['cep'],
        ];
    }
}
