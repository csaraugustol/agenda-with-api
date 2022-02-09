<?php

namespace App\Services\Contracts;

use App\Services\Responses\ServiceResponse;

interface VExpensesComunicationServiceInterface
{
    public function tokenToAccessVExpenses(string $userId): ServiceResponse;
}
