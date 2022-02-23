<?php

namespace App\Services\Params\Contact;

use App\Services\Params\BaseServiceParams;

class CreateCompleteContactsServiceParams extends BaseServiceParams
{
    public $name;
    public $user_id;
    public $external_id;
    public $phones;
    public $adresses;
    public $tags;

    /**
     * Argumentos necessários para criação do contato completo
     *
     * @param string      $name
     * @param string      $user_id
     * @param array       $phones
     * @param array       $adresses
     * @param array|null  $tags
     * @param string|null $external_id
     */
    public function __construct(
        string $name,
        string $user_id,
        array $phones,
        array $adresses,
        ?array $tags,
        string $external_id = null
    ) {
        parent::__construct();
    }
}
