<?php

namespace App\Services\Params\Address;

use App\Services\Params\BaseServiceParams;

class CreateAddressServiceParams extends BaseServiceParams
{
    public $street_name;
    public $number;
    public $complement;
    public $neighborhood;
    public $city;
    public $state;
    public $postal_code;
    public $country;
    public $contact_id;

    /**
     * Argumentos necessários para criação do endereço
     *
     * @param string $street_name
     * @param int    $number
     * @param string $complement
     * @param string $neighborhood
     * @param string $city
     * @param string $state
     * @param string $postal_code
     * @param string $country
     * @param string $contact_id
     */
    public function __construct(
        string $street_name,
        int $number,
        string $complement,
        string $neighborhood,
        string $city,
        string $state,
        string $postal_code,
        string $country,
        string $contact_id
    ) {
        parent::__construct();
    }
}
