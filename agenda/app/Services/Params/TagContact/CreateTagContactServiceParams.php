<?php

namespace App\Services\Params\TagContact;

use App\Services\Params\BaseServiceParams;

class CreateTagContactServiceParams extends BaseServiceParams
{
    public $tag_id;
    public $contact_id;

    /**
     * Argumentos necessários para criação do
     * relacionamento da tag com o contato do usuário
     *
     * @param string $tag_id
     * @param string $contact_id
     */
    public function __construct(
        string $tag_id,
        string $contact_id
    ) {
        parent::__construct();
    }
}
