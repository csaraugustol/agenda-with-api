<?php

namespace App\Services\Contracts;

use App\Services\Params\Vexpenses\AccessTokenServiceParams;
use App\Services\Responses\ServiceResponse;

interface VExpensesComunicationServiceInterface
{
    public function tokenToAccessVexpenses(AccessTokenServiceParams $accessTokenServiceParams): ServiceResponse;
}
