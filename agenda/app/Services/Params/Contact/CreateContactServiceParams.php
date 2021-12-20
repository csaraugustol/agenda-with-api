<?php

namespace App\Services\Params\Contact;

use App\Services\Params\BaseServiceParams;

class CreateContactServiceParams extends BaseServiceParams
{
    public $name;
    public $user_id;

    /**
     * Argumentos necessários para criação do contato
     *
     * @param string $name
     * @param string $user_id
     */
    public function __construct(
        string $name,
        string $user_id
    ) {
        parent::__construct();
    }
}
