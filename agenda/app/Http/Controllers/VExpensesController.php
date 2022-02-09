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
     * GET /vexpenses/generate-access-token
     *
     * @return JsonResponse
     */
    public function VExpensesAccessToken(): JsonResponse
    {
        $createAccessTokenResponse = $this->vExpensesComunicationService->tokenToAccessVExpenses(
            user('id')
        );

        if (!$createAccessTokenResponse->success || is_null($createAccessTokenResponse->data)) {
            return $this->errorResponseFromService($createAccessTokenResponse);
        }

        return $this->response(new DefaultResponse(
            new ExternalTokenResource($createAccessTokenResponse->data)
        ));
    }
}
