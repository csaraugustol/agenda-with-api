<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\ExternalToken\ExternalTokenResource;
use App\Services\Contracts\VExpensesComunicationServiceInterface;

class VExpensesController extends ApiController
{
    /**
     * @var VExpensesComunicationServiceInterface
     */
    protected $vExpensesComunicationService;

    public function __construct(VExpensesComunicationServiceInterface $vExpensesComunicationService)
    {
        $this->vExpensesComunicationService = $vExpensesComunicationService;
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
        $createTokenResponse = $this->vExpensesComunicationService->tokenToAccessVExpenses(
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
