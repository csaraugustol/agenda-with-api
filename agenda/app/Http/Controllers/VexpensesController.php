<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\Vexpenses\VexpensesResource;
use App\Http\Requests\Vexpenses\AccessTokenRequest;
use App\Services\Contracts\VexpensesServiceInterface;
use App\Http\Resources\Vexpenses\TeamMembersCollectionResource;

class VexpensesController extends ApiController
{
    /**
     * @var VexpensesServiceInterface
     */
    protected $vexpensesService;

    public function __construct(VexpensesServiceInterface $vexpensesService)
    {
        $this->vexpensesService = $vexpensesService;
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
        $accessResponse = $this->vexpensesService->tokenToAccess(
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

    /**
     * Retorna todos os membros do VExpenses
     *
     * GET /vexpenses/team-members
     *
     * @return JsonResponse
     */
    public function teamMembers(): JsonResponse
    {
        $teamMembersResponse = $this->vexpensesService->sendRequest('team-members');

        if (!$teamMembersResponse->success || is_null($teamMembersResponse->data)) {
            return $this->errorResponseFromService($teamMembersResponse);
        }

        return $this->response(new DefaultResponse(
            new TeamMembersCollectionResource($teamMembersResponse->data)
        ));
    }
}
