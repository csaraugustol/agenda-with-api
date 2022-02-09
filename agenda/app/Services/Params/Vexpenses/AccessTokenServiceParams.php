<?php

namespace App\Services\Params\Vexpenses;

use App\Services\Params\BaseServiceParams;

class AccessTokenServiceParams extends BaseServiceParams
{
    public $token;
    public $user_id;
    public $system;
    public $expires_at;
    public $clear_rectroativics_tokens;

    /**
     * Argumento necessários para criação do token de acesso
     *
     * @param string $token
     * @param string $user_id
     * @param string $system
     * @param bool   $expires_at
     * @param bool   $clear_rectroativics_tokens
     */
    public function __construct(
        string $token,
        string $user_id,
        string $system,
        bool $expires_at,
        bool $clear_rectroativics_tokens
    ) {
        parent::__construct();
    }
}
