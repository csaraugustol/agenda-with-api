<?php

namespace App\Services\Params\Tag;

use App\Services\Params\BaseServiceParams;

class CreateTagServiceParams extends BaseServiceParams
{
    public $description;
    public $user_id;

    /**
     * Argumentos necessários para criação da tag
     *
     * @param string $description
     * @param string $user_id
     */
    public function __construct(
        string $description,
        string $user_id
    ) {
        parent::__construct();
    }
}
