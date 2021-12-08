<?php

namespace App\Services\Params\Phone;

use App\Services\Params\BaseServiceParams;

class CreatePhoneServiceParams extends BaseServiceParams
{
    public $phone_number;
    public $contact_id;

    /**
     * Argumentos necessários para criação do telefone
     *
     * @param string $phone_number
     * @param string $contact_id
     */
    public function __construct(
        string $phone_number,
        string $contact_id
    ) {
        parent::__construct();
    }
}
