<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\Vexpenses\VexpensesResource;
use App\Http\Requests\Vexpenses\AccessTokenRequest;
use App\Services\Contracts\VexpensesServiceInterface;

class VexpensesController extends ApiController
{
    /**
     * @var VexpensesServiceInterface
     */
    protected $VexpensesService;

    public function __construct(VexpensesServiceInterface $VexpensesService)
    {
        $this->VexpensesService = $VexpensesService;
    }

    /**
     * Seta o token para acessar o VExpenses
     *
     * POST /vexpenses/access-token
     *
     * @return JsonResponse
     */
    public function accessToken(AccessTokenRequest $request): JsonResponse
    {
        $accessResponse = $this->VexpensesService->tokenToAccess(
            $request->token,
            user('id')
        );

        if (!$accessResponse->success || is_null($accessResponse->data)) {
            return $this->errorResponseFromService($accessResponse);
        }

        return $this->response(new DefaultResponse(
            new VexpensesResource($accessResponse->data)
        ));
    }
}
