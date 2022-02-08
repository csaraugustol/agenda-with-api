<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Services\Contracts\VExpensesServiceInterface;
use App\Http\Resources\ExternalToken\ExternalTokenResource;

class VExpensesController extends ApiController
{
    /**
     * @var VExpensesServiceInterface
     */
    protected $vExpensesService;

    public function __construct(VExpensesServiceInterface $vExpensesService)
    {
        $this->vExpensesService = $vExpensesService;
    }

    /**
     * Retorna o token para acessar o VExpenses
     *
     * GET /users/vexpenses-access-token
     *
     * @return JsonResponse
     */
    public function VExpensesAccessToken(): JsonResponse
    {
        $createTokenResponse = $this->vExpensesService->tokenToAccessVExpenses(
            user('id')
        );

        if (!$createTokenResponse->success || is_null($createTokenResponse->data)) {
            return $this->errorResponseFromService($createTokenResponse);
        }

        return $this->response(new DefaultResponse(
            new ExternalTokenResource($createTokenResponse->data)
        ));
    }
}
