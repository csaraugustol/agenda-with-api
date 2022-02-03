<?php

namespace App\Services\Contracts;

use GuzzleHttp\Client;

interface BaseServiceInterface
{
    public function setClient(Client $client);
}
