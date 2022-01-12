<?php

namespace Tests;

use App\Services\Responses\ServiceResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Assertion para verificar se um internal error existe dentro
     * da response da service
     *
     * @param ServiceResponse $serviceResponse
     * @param integer $code
     *
     * @return void
     */
    public function assertHasInternalError(ServiceResponse $serviceResponse, int $code): void
    {
        $hasError = collect($serviceResponse->internalErrors)
            ->contains('code', $code);

        $this->assertTrue($hasError, "O Internal Error \"($code)\" não foi encontrado");
    }
}
