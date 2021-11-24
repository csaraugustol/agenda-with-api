<?php

namespace App\Services\Params\User;

use App\Services\Params\BaseServiceParams;

class RegisterUserServiceParams extends BaseServiceParams
{
    public $name;
    public $email;
    public $password;

    /**
     * Argumento necessários para criação do usuário
     *
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(
        string $name,
        string $email,
        string $password
    ) {
        parent::__construct();
    }
}
