<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\Vexpenses\VexpensesResource;
use App\Http\Requests\Vexpenses\AccessTokenRequest;
use App\Services\Params\Vexpenses\AccessTokenServiceParams;
use App\Services\Contracts\VExpensesComunicationServiceInterface;

class VexpensesController extends ApiController
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
     * POST /vexpenses/access-token
     *
     * @return JsonResponse
     */
    public function accessToken(AccessTokenRequest $request): JsonResponse
    {
        $accessResponse = $this->vExpensesComunicationService->tokenToAccessVexpenses(
            new AccessTokenServiceParams(
                $request->token,
                user('id'),
                $request->system,
                $request->expires_at,
                $request->clear_rectroativics_tokens,
            )
        );

        if (!$accessResponse->success || is_null($accessResponse->data)) {
            return $this->errorResponseFromService($accessResponse);
        }

        return $this->response(new DefaultResponse(
            new VexpensesResource($accessResponse->data)
        ));
    }
}
