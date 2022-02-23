<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\StoreRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\Vexpenses\VexpensesResource;
use App\Http\Requests\Vexpenses\AccessTokenRequest;
use App\Http\Resources\Contact\ContactDetailsResource;
use App\Services\Contracts\VexpensesServiceInterface;
use App\Http\Resources\Vexpenses\TeamMembersCollectionResource;
use App\Http\Resources\Vexpenses\TeamMembersResource;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

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
        $teamMembersResponse = $this->vexpensesService->findAllTeamMembers(
            user('id')
        );

        if (!$teamMembersResponse->success || is_null($teamMembersResponse->data)) {
            return $this->errorResponseFromService($teamMembersResponse);
        }

        return $this->response(new DefaultResponse(
            new TeamMembersCollectionResource($teamMembersResponse->data)
        ));
    }

    /**
     * Retorna um membro do VExpenses
     *
     * GET /vexpenses/team-members/{id}
     *
     * @return JsonResponse
     */
    public function teamMember(string $externalId): JsonResponse
    {
        $teamMembersResponse = $this->vexpensesService->findTeamMember(
            user('id'),
            $externalId
        );

        if (!$teamMembersResponse->success || is_null($teamMembersResponse->data)) {
            return $this->errorResponseFromService($teamMembersResponse);
        }

        return $this->response(new DefaultResponse(
            new TeamMembersResource($teamMembersResponse->data)
        ));
    }

    /**
     * Cria um contato com o membro do VExpenses
     *
     * POST /vexpenses/team-members/{id}
     *
     * @return JsonResponse
     */
    public function storeContactWithMember(StoreRequest $request, string $externalId): JsonResponse
    {
        $completeContactParams = new CreateCompleteContactsServiceParams(
            $request->name,
            user('id'),
            $request->phones,
            $request->adresses,
            $request->tags,
            $externalId
        );

        $createMemberContactResponse = $this->vexpensesService->store(
            $completeContactParams
        );

        if (!$createMemberContactResponse->success || is_null($createMemberContactResponse->data)) {
            return $this->errorResponseFromService($createMemberContactResponse);
        }

        return $this->response(new DefaultResponse(
            new ContactDetailsResource($createMemberContactResponse->data)
        ));
    }
}
