<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;

class TestController extends ApiController
{
    /**
     * Página de teste de rota
     *
     * * GET /test
     *
     * @return JsonResponse
     */
    public function test(): JsonResponse
    {
        return $this->response(new DefaultResponse());
    }
}
